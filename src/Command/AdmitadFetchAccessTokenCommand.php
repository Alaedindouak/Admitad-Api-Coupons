<?php

namespace App\Command;

use App\Entity\AdmitadClientAuth;
use App\Service\AdmitadApiClientAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AdmitadFetchAccessTokenCommand extends Command
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SerializerInterface */
    private $serializer;

    /** @var AdmitadApiClientAuth */
    private $admitadApiClientAuth;

    protected static $defaultName = 'admitad:fetch-access-token';

    protected static $defaultDescription = 'Retrieve an access token from Admitad Auth Api';


    public function __construct(EntityManagerInterface $entityManager,
                                AdmitadApiClientAuth $admitadApiClientAuth,
                                SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->admitadApiClientAuth = $admitadApiClientAuth;

        parent::__construct();

    }

    protected function configure(): void
    {
        $this->addArgument(
            'scope',
            InputArgument::IS_ARRAY,
            "['websites', 'coupons_for_website']"
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->getDescription());

        // 1. ping Admitad API and grab the response
        $admitadClientAuth = $this->admitadApiClientAuth->fetchAccessToken($input->getArgument('scope'));

        // 1.1 handle non 200 status code response
        if ($admitadClientAuth->getStatusCode() !== 200)
        {
            $output->writeln($admitadClientAuth->getContent());
            return Command::FAILURE;
        }

        // find at least one record in db if it's there, remove it and create new one
        $username =json_decode($admitadClientAuth->getContent())->username ?? null;

        if ( $client = $this->entityManager->getRepository(AdmitadClientAuth::class)->findOneBy(['username' => $username]) )
        {
            $clientAuth = $this->serializer->deserialize(
                $admitadClientAuth->getContent(),
                AdmitadClientAuth::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $client]
            );

        }
        else
        {
            $clientAuth = $this->serializer->deserialize(
                $admitadClientAuth->getContent(),
                AdmitadClientAuth::class,
                'json'
            );
        }

        $this->entityManager->persist($clientAuth);
        $this->entityManager->flush();

        $output->writeln( $clientAuth->getAccessToken() . '  has been saved / updated.');

        return Command::SUCCESS;
    }
}
