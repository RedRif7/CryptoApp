<?php

namespace Crypto;

use GuzzleHttp\Client;
use Dotenv\Dotenv;

class CoinMarketCapApi implements CryptoApi
{
    private Client $client;
    private string $apiKey;
    private string $url;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $this->client = new Client();
        $this->apiKey = $_ENV['COINMARKET_API_KEY'];
        $this->url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
    }

    public function getCryptoPrice(string $symbol): float
    {
        $cryptos = $this->getTopCryptos();
        foreach ($cryptos as $crypto) {
            if ($crypto['symbol'] === $symbol) {
                return $crypto['price'];
            }
        }
        return 0.0;
    }

    public function getTopCryptos(): array
    {
        $response = $this->client->get($this->url, [
            'headers' => [
                'X-CMC_PRO_API_KEY' => $this->apiKey,
                'Accept' => 'application/json'
            ],
            'query' => [
                'start' => 1,
                'limit' => 10,
                'convert' => 'USD'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        $cryptos = [];
        foreach ($data['data'] as $crypto) {
            $cryptos[] = [
                'id' => $crypto['id'],
                'name' => $crypto['name'],
                'symbol' => $crypto['symbol'],
                'price' => $crypto['quote']['USD']['price']
            ];
        }

        return $cryptos;
    }

    public function searchCrypto(string $symbol, array $cryptos)
    {
        $crypto = $this->getCryptoBySymbol($symbol, $cryptos);

        if (!$crypto) {
            echo "Cryptocurrency with symbol '$symbol' not found.\n";
            return;
        }

        return $crypto;
    }

    public function getCryptoBySymbol(string $symbol, array $cryptos)
    {
        foreach ($cryptos as $crypto) {
            if ($crypto['symbol'] === $symbol) {
                return $crypto;
            }
        }
        return null;
    }
}
