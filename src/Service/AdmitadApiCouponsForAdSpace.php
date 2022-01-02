<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdmitadApiCouponsForAdSpace
{

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var SerializerInterface */
    private $serializer;

    private const URL = 'https://api.admitad.com/coupons/website/';
    // private const RU = 'RU';
    private const PROMOCOD = 'promocode';

    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function fetchCouponsForPromokodsAdSpace($accessToken, $adSpaceId): JsonResponse
    {
        $couponResponse = [];

        $response = $this->httpClient->request('GET', self::URL . $adSpaceId . '/', [
            'query' => [
                'limit' => 500
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse('Admitad Api Error', 400);
        }

        $coupons = json_decode($response->getContent())->results;

        foreach ($coupons as $coupon)
        {
           if($coupon->species === self::PROMOCOD)
           {
               $couponResponse[] = [
                   'name' => $coupon->name,
                   'promocode' => $coupon->promocode,
                   'promocode_id' => $coupon->id,
                   'description' => $coupon->description,
                   'discount' => $coupon->discount,
                   'image' => $coupon->image,
                   'link' => $coupon->goto_link,
                   'date_start' => $coupon->date_start,
                   'date_end' => $coupon->date_end
               ];
           }
        }

        $responseData = new JsonResponse($couponResponse, Response::HTTP_OK);
        $responseData->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

        return $responseData;
    }

}