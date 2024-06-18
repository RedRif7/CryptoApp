<?php

namespace Crypto;

use LucidFrame\Console\ConsoleTable;
use GuzzleHttp\Client;

class CoinMarketCapApi implements CryptoApi
{
    private Client $client;
    private string $apiKey;
    private string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = '868535e9-2644-4b1e-9209-3363a0da0dd0'; // Replace with your actual CoinMarketCap API key
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

        $table = new ConsoleTable();
        $table->setHeaders(['Name', 'Symbol', 'Price']);

        $table->addRow([$crypto['name'], $crypto['symbol'], '$' . number_format($crypto['price'], 2)]);

        echo "Crypto Information for $symbol:\n";
        $table->display();
    }

    private function getCryptoBySymbol(string $symbol, array $cryptos)
    {
        foreach ($cryptos as $crypto) {
            if ($crypto['symbol'] === $symbol) {
                return $crypto;
            }
        }
        return null;
    }

    public function displayTopCryptos(array $cryptos)
    {
        $table = new ConsoleTable();
        $table->setHeaders(['ID', 'Name', 'Symbol', 'Price']);

        foreach ($cryptos as $index => $crypto) {
            $table->addRow([$index + 1, $crypto['name'], $crypto['symbol'], '$' . number_format($crypto['price'], 2)]);
        }

        echo "Top Cryptocurrencies:\n";
        $table->display();
    }
}
