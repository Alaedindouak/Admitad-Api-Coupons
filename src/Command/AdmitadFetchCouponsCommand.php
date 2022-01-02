<?php

namespace App\Command;

use App\Entity\AdmitadClientAuth;
use App\Entity\AdmitadCoupon;
use App\Service\AdmitadApiAdSpaces;
use App\Service\AdmitadApiCouponsForAdSpace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AdmitadFetchCouponsCommand extends Command
{
    protected static $defaultName = 'admitad:fetch-coupons';
    protected static $defaultDescription = 'Retrieve a coupons from Admitad Api';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AdmitadApiCouponsForAdSpace */
    private $apiCouponsForAdSpace;

    /** @var AdmitadApiAdSpaces */
    private $adSpaces;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(AdmitadApiAdSpaces $adSpaces,
                                SerializerInterface $serializer,
                                EntityManagerInterface $entityManager,
                                AdmitadApiCouponsForAdSpace $apiCouponsForAdSpace)
    {
        $this->adSpaces = $adSpaces;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->apiCouponsForAdSpace = $apiCouponsForAdSpace;

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $promocodeNumber = 0;
        $token = $this->getAccessToken();
        $adSpaceId = $this->getPromokodsAdSpaceId($token);

        $promocodes = $this->apiCouponsForAdSpace->fetchCouponsForPromokodsAdSpace($token, $adSpaceId);

        if ($promocodes->getStatusCode() !== 200) {
            $output->writeln($promocodes->getContent());
            return Command::FAILURE;
        }

        $promocodeAsArray = json_decode($promocodes->getContent(), true);

        foreach ($promocodeAsArray as $promocode)
        {
            $promocodeId = $promocode['promocode_id'] ?? null;

            $promocodeAsJson = json_encode(
                $promocode,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
            );

            if ($promocodeInDb = $this->entityManager->getRepository(AdmitadCoupon::class)->findOneBy(['promocodeId' => $promocodeId]))
            {
                $data = $this->serializer->deserialize(
                    $promocodeAsJson,
                    AdmitadCoupon::class,
                    'json',
                    [
                        AbstractNormalizer::OBJECT_TO_POPULATE => $promocodeInDb,
                        AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true
                    ]
                );
            }
            else
            {
                $data = $this->serializer->deserialize(
                    $promocodeAsJson,
                    AdmitadCoupon::class,
                    'json',
                    [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
                );
            }


            $this->entityManager->persist($data);
            $this->entityManager->flush();
            $promocodeNumber++;
        }

        $output->writeln( "{$promocodeNumber} has been saved / updated.");

        return Command::SUCCESS;
    }

    private function getAccessToken(): string {

        return $this->entityManager
            ->getRepository(AdmitadClientAuth::class)
            ->createQueryBuilder('admitad_client_auth')
            ->select('admitad_client_auth.accessToken')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getPromokodsAdSpaceId($accessToken): int {

        $adSpaceContent = json_decode( $this->adSpaces
            ->fetchClientAdScpaes($accessToken)
            ->getContent());
        return $adSpaceContent->id;
    }
}
