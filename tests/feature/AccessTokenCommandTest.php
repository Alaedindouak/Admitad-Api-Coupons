<?php

namespace App\Tests\feature;

use App\Entity\AdmitadClientAuth;
use App\Service\AdmitadApiClientAuth;
use App\Tests\DatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AccessTokenCommandTest extends DatabaseTestCase
{

    /** @test */
    public function the_access_token_command_behave_correctly_when_access_token_does_not_exist()
    {
        // set up
        $application = new Application(self::$kernel);

        // find our command
        $command = $application->find('admitad:fetch-access-token');
        $commandTester = new CommandTester($command);

        // do sth
        $commandTester->execute([
           'scope' => ['advcampaigns', 'banners', 'websites'],
        ]);

        // make assertions
        $repos = $this->entityManager->getRepository(AdmitadClientAuth::class);

        /** @var AdmitadClientAuth $token */
        $token = $repos->findOneBy(['username' => 'alaedin']);

        $this->assertEquals('alaedin', $token->getUsername());
        $this->assertEquals($token->getAccessToken(), $token->getAccessToken());
        $this->assertEquals('bearer', $token->getTokenType());
        $this->assertEquals(604800, $token->getExpiredTime());
        $this->assertEquals($token->getRefreshToken(), $token->getRefreshToken());
        $this->assertEquals(['advcampaigns', 'banners', 'websites'], $token->getScopes());

    }
    
    /** @test  */
    public function non_200_status_code_responses_are_handled_correctly()
    {
        // set up
        $application = new Application(self::$kernel);

        // command
        $command = $application->find('admitad:fetch-access-token');
        $commandTester = new CommandTester($command);

        // non 200 response
        AdmitadApiClientAuth::$statusCode = 500;
        // error content

        // do sth
        $commandStatus = $commandTester->execute([
            'scope' => ['advcampaigns', 'banners', 'websites'],
        ]);

        $repos = $this->entityManager->getRepository(AdmitadClientAuth::class);

        $admitadClientAuthRecordCount = $repos->createQueryBuilder('admitad_client_auth')
            ->select('count(admitad_client_auth.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // make assertions
        $this->assertEquals(1, $commandStatus); // the process failed  1 -> Command::FAILURE
        $this->assertEquals(1, $commandStatus); // no records have been saved in DB 0 -> Command::SUCCESS
    }
}