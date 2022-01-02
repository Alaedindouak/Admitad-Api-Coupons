<?php

namespace App\Tests\integration;

use App\Tests\DatabaseTestCase;

class AdmitadApiClientAuthTest extends DatabaseTestCase
{
    /**
     * @test
     * @group integration
     */
    public function the_admitad_api_client_auth_return_correct_data()
    {
        // set up
        // need AdmitadApiClientAuth
        $admitadApiClientAuth = self::$kernel->getContainer()->get('admitad-api-client-auth');

        // do sth
        $response = $admitadApiClientAuth->fetchAccessToken(['advcampaigns', 'banners', 'websites']);

        $admitadClientAuth = json_decode($response->getContent());
        // make assertions

         //dd($admitadClientAuth);

        $this->assertSame('alaedin', $admitadClientAuth->username);
        $this->assertSame($admitadClientAuth->accessToken, $admitadClientAuth->accessToken);
        $this->assertSame('bearer', $admitadClientAuth->tokenType);
        $this->assertSame($admitadClientAuth->refreshToken, $admitadClientAuth->refreshToken);
        $this->assertIsInt(604800, $admitadClientAuth->expiredTime);
        $this->assertSame(['advcampaigns', 'banners', 'websites'], $admitadClientAuth->scopes);

    }

}