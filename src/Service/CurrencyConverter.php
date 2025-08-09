<?php

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverter
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiKey,
    )
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }


    public function getAllExchangeRates(string $baseCurrency): array
    {
        try {
            $response = $this->httpClient->request('GET',
                "https://api.currencyapi.com/v3/latest", [
                    'headers' => [
                        'apiKey' => $this->apiKey,
                        ],
                    'query' => [
                        'base_currency' => $baseCurrency,
                        'amount' => 1
                    ]
                ]);
            $content = $response->toArray();
        } catch (Exception $e) {
            throw new Exception("Erreur de communication avec l'API de devises : " . $e->getMessage());
        }

        return $content['data'];
    }

}