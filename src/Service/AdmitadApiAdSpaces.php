<?php

namespace App\Service;

use App\Helper\AdSpaceStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdmitadApiAdSpaces
{
    /** @var HttpClientInterface */
    private $httpClient;

    private const URL = 'https://api.admitad.com/websites/v2/';
    private static $statusCode = 200;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchClientAdScpaes(string $accessToken): JsonResponse
    {
        $response = $this->httpClient->request('GET', self::URL, [
            'query' => [
                'status' => AdSpaceStatus::active
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse('Admitad Api Error', 400);
        }

        $admitadAdSpacesResponse = json_decode($response->getContent());

        $clientAdSpaces = [];

        foreach ($admitadAdSpacesResponse as $adSpaceResponse) {

            $clientAdSpaces = [
                'id' => $adSpaceResponse->id,
                'name' => $adSpaceResponse->name
            ];
        }

        return new JsonResponse($clientAdSpaces, self::$statusCode);
    }
}