<?php

namespace App\Tests;

use App\Entity\AdmitadClientAuth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdmitadClientAuthTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function an_access_token_can_be_created_in_database()
    {
        // setup
        $admitadClientAuth = new AdmitadClientAuth();

        $admitadClientAuth->setUsername('webmaster1');
        $admitadClientAuth->setAccessToken('4b8b33955a');
        $admitadClientAuth->setTokenType('bearer');
        $admitadClientAuth->setExpiredTime(604800);
        $admitadClientAuth->setRefreshToken('ea957cce42');
        $admitadClientAuth->setScopes(['advcampaigns', 'banners', 'websites']);


        // do sth
        $this->entityManager->persist($admitadClientAuth);
        $this->entityManager->flush();

        $admitadClientAuthRepos = $this->entityManager->getRepository(AdmitadClientAuth::class);
        $admitadClientAuthRecord = $admitadClientAuthRepos->findOneBy(['accessToken' => '4b8b33955a']);

        //make assertions
        $this->assertEquals('webmaster1', $admitadClientAuthRecord->getUsername());
        $this->assertEquals('4b8b33955a', $admitadClientAuthRecord->getAccessToken());
        $this->assertEquals('bearer', $admitadClientAuthRecord->getTokenType());
        $this->assertEquals(604800, $admitadClientAuthRecord->getExpiredTime());
        $this->assertEquals('ea957cce42', $admitadClientAuthRecord->getRefreshToken());
        $this->assertEquals(['advcampaigns', 'banners', 'websites'], $admitadClientAuthRecord->getScopes());
    }
}