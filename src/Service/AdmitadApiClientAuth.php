<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdmitadApiClientAuth
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    public static $statusCode = 200;

    private const URL = 'https://api.admitad.com/token/';
    private const GRANT_TYPE = 'client_credentials';

    public function __construct(HttpClientInterface $httpClient, $clientId, $clientSecret)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function fetchAccessToken(array $scopes): JsonResponse
    {
        $auth_basic64 = base64_encode($this->clientId . ':' . $this->clientSecret);

        $response = $this->httpClient->request('POST', self::URL, [
            'query' => [
                'client_id' => $this->clientId,
                'grant_type' => self::GRANT_TYPE,
                'scope' => implode(' ', $scopes)
            ],
            'headers' => [
                'Authorization' => 'Basic ' . $auth_basic64,
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse('Admitad Api Error', 400);
        }

        $admitadClientAuthResponse = json_decode($response->getContent());

        $admitadClientAuth = [
            'username' => $admitadClientAuthResponse->username,
            'accessToken' => $admitadClientAuthResponse->access_token,
            'tokenType' => $admitadClientAuthResponse->token_type,
//            'refreshToken' => $admitadClientAuthResponse->refresh_token,
            'expiredTime' => $admitadClientAuthResponse->expires_in,
            'scopes' => explode(' ', $admitadClientAuthResponse->scope)
        ];

        return new JsonResponse($admitadClientAuth, self::$statusCode);
    }
}