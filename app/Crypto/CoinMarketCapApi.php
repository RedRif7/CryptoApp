<?php

namespace Crypto;

use Dotenv\Dotenv;
class CoinMarketCapApi implements CryptoApi
{
    private string $apiKey;
    private string $baseUrl;
    private array $headers;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__. '/../../' );
        $dotenv->load();

        $this->apiKey = $_ENV['COINMARKET_API_KEY'];
        $this->baseUrl = 'https://pro-api.coinmarketcap.com/v1/';
        $this->headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $this->apiKey
        ];
    }

    private function makeRequest(string $endpoint, array $parameters = []): ?object {
        $url = $this->baseUrl . $endpoint . '?' . http_build_query($parameters);
        $contextOptions = [
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $this->headers)
            ]
        ];
        $context = stream_context_create($contextOptions);
        $response = file_get_contents($url, false, $context);
        return $response ? json_decode($response) : null;
    }

    public function getTopCryptos(int $limit = 10): array {
        $data = $this->makeRequest('cryptocurrency/listings/latest', [
            'start' => '1',
            'limit' => $limit,
            'convert' => 'USD'
        ]);
        $currencies = [];
        if ($data && isset($data->data)) {
            foreach ($data->data as $crypto) {
                $currencies[] = new Currency(
                    $crypto->name,
                    $crypto->symbol,
                    $crypto->quote->USD->price,
                    $crypto->quote->USD->market_cap,
                    $crypto->quote->USD->volume_24h
                );
            }
        }
        return $currencies;
    }

    public function getCryptoPrice(string $symbol): ?float {
        $data = $this->makeRequest('cryptocurrency/quotes/latest', [
            'symbol' => $symbol,
            'convert' => 'USD'
        ]);
        return $data->data->$symbol->quote->USD->price ?? null;
    }

    public function searchCrypto(string $symbol): ?Currency {
        $data = $this->makeRequest('cryptocurrency/quotes/latest', [
            'symbol' => $symbol,
            'convert' => 'USD'
        ]);
        if ($data && isset($data->data->$symbol)) {
            $crypto = $data->data->$symbol;
            return new Currency(
                $crypto->name,
                $crypto->symbol,
                $crypto->quote->USD->price,
                $crypto->quote->USD->market_cap,
                $crypto->quote->USD->volume_24h
            );
        }
        return null;
    }
}
