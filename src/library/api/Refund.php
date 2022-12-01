<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Paco\PhpDemo\ActionRequest;
use Paco\PhpDemo\SecurityData;

class Refund extends ActionRequest
{
    /**
     * @throws GuzzleException
     */
    public function Execute(): string
    {
        $officeId = "DEMOOFFICE";
        $orderNo = "1643362945100"; //OrderNo can be Refund one time only

        $actionBy = "System|c88ef0dc-14ea-4556-922b-7f62a6a3ec9e";
        $actionEmail = "babulal.cho@2c2pexternal.com";

        $request = [
            "refundAmount" => [
                "AmountText" => "000000100000",
                "CurrencyCode" => "THB",
                "DecimalPlaces" => 2,
                "Amount" => 1000.00
            ],
            "refundItems" => [],
            "localMakerChecker" => [
                "maker" => [
                    "username" => $actionBy,
                    "email" => $actionEmail
                ]
            ],
            "officeId" => $officeId,
            "orderNo" => $orderNo,
        ];

        $stringRequest = json_encode($request);

        echo $stringRequest;

        //third-party http client https://github.com/guzzle/guzzle
        $response = $this->client->post('api/1.0/Refund/refund', [
            'headers' => [
                'Accept' => 'application/json',
                'apiKey' => SecurityData::$AccessToken,
                'Content-Type' => 'application/json; charset=utf-8'
            ],
            'body' => $stringRequest
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function ExecuteJose(): string
    {
        $now = Carbon::now();
        $officeId = "DEMOOFFICE";
        $orderNo = "1643362945100"; //OrderNo can be Refund one time only

        $actionBy = "System|c88ef0dc-14ea-4556-922b-7f62a6a3ec9e";
        $actionEmail = "babulal.cho@2c2pexternal.com";

        $request = [
            "refundAmount" => [
                "AmountText" => "000000100000",
                "CurrencyCode" => "THB",
                "DecimalPlaces" => 2,
                "Amount" => 1000.00
            ],
            "refundItems" => [],
            "localMakerChecker" => [
                "maker" => [
                    "username" => $actionBy,
                    "email" => $actionEmail
                ]
            ],
            "officeId" => $officeId,
            "orderNo" => $orderNo,
        ];

        $payload = [
            "request" => $request,
            "iss" => SecurityData::$AccessToken,
            "aud" => "PacoAudience",
            "CompanyApiKey" => SecurityData::$AccessToken,
            "iat" => $now->unix(),
            "nbf" => $now->unix(),
            "exp" => $now->addHour()->unix(),
        ];

        $stringPayload = json_encode($payload);
        $signingKey = $this->GetPrivateKey(SecurityData::$MerchantSigningPrivateKey);
        $encryptingKey = $this->GetPublicKey(SecurityData::$PacoEncryptionPublicKey);

        $body = $this->EncryptPayload($stringPayload, $signingKey, $encryptingKey);

        //third-party http client https://github.com/guzzle/guzzle
        $response = $this->client->post('api/1.0/Refund/refund', [
            'headers' => [
                'Accept' => 'application/jose',
                'CompanyApiKey' => SecurityData::$AccessToken,
                'Content-Type' => 'application/jose; charset=utf-8'
            ],
            'body' => $body
        ]);

        $token = $response->getBody()->getContents();
        $decryptingKey = $this->GetPrivateKey(SecurityData::$MerchantDecryptionPrivateKey);
        $signatureVerificationKey = $this->GetPublicKey(SecurityData::$PacoSigningPublicKey);

        return $this->DecryptToken($token, $decryptingKey, $signatureVerificationKey);
    }

}