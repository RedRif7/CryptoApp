<?php
require 'vendor/autoload.php';

use LucidFrame\Console\ConsoleTable;
use Carbon\Carbon;

class CryptoApp {
    private string $apiKey;
    private string $baseUrl;
    private float $balance;
    private array $headers;

    public function __construct() {
        $this->apiKey = '868535e9-2644-4b1e-9209-3363a0da0dd0';
        $this->baseUrl = 'https://pro-api.coinmarketcap.com/v1/';
        $this->balance = 1000.00;
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

    public function displayTopTrendingCryptos() {
        $endpoint = 'cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1',
            'limit' => '10',
            'convert' => 'USD'
        ];
        $data = $this->makeRequest($endpoint, $parameters);
        if ($data && isset($data->data)) {
            $table = new ConsoleTable();
            $table->setHeaders(['ID','UCID', 'Name', 'Price/Coin']);
            foreach ($data->data as $crypto) {
                $table->addRow([
                    $id += 1,
                    $crypto->id,
                    $crypto->name,
                    $crypto->quote->USD->price > 1 ?
                        "$" .number_format($crypto->quote->USD->price, 2) :
                        "$" .number_format($crypto->quote->USD->price, 10),
                ]);
            }
            $table->display();
        } else {
            echo "Failed to retrieve data from CoinMarketCap API.\n";
        }
    }

    public function checkBalance() {
        echo "Current Balance: $" . number_format($this->balance, 2) . "\n";
    }

    public function searchCrypto(string $shortName) {
        $endpoint = 'cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => $shortName,
            'convert' => 'USD'
        ];
        $data = $this->makeRequest($endpoint, $parameters);
        if ($data && isset($data->data->$shortName)) {
            $crypto = $data->data->$shortName;
            echo $crypto->name;
            echo $crypto->quote->USD->price > 1 ?
                " $".number_format($crypto->quote->USD->price, 2) . "\n" :
                " $".number_format($crypto->quote->USD->price, 10) . "\n";
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }

    public function buyCrypto(string $shortName) {
        $endpoint = 'cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => $shortName,
            'convert' => 'USD'
        ];
        $data = $this->makeRequest($endpoint, $parameters);
        if ($data && isset($data->data->$shortName)) {
            $crypto = $data->data->$shortName;
            $price = $crypto->quote->USD->price;
            echo "Your balance - $$this->balance \n";
            echo "Current price of $shortName: $" . number_format($price, 2) . "\n";
            $amount = (float) readline("Enter the amount to buy: ");
            $totalCost = $amount * $price;
            if ($totalCost <= $this->balance) {
                $this->balance -= $totalCost;
                $order = (object) [
                    'time' => Carbon::now()->toDateTimeString(),
                    'crypto' => $shortName,
                    'price' => $price,
                    'action' => 'buy',
                    'amount' => $amount,
                    'total' => $totalCost
                ];
                $this->logOrder($order);
                echo "Bought $amount of $shortName at $$price each for a total of $$totalCost.\n";
            } else {
                echo "Insufficient balance.\n";
            }
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }

    public function sellCrypto(string $shortName) {
        $endpoint = 'cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => $shortName,
            'convert' => 'USD'
        ];
        $data = $this->makeRequest($endpoint, $parameters);
        if ($data && isset($data->data->$shortName)) {
            $crypto = $data->data->$shortName;
            $price = $crypto->quote->USD->price;
            echo "Your balance - $$this->balance \n";
            echo "Current price of $shortName: $" . number_format($price, 2) . "\n";
            $amount = (float) readline("Enter the amount to sell: ");
            $totalEarnings = $amount * $price;
            $this->balance += $totalEarnings;
            $order = (object) [
                'time' => Carbon::now()->toDateTimeString(),
                'crypto' => $shortName,
                'price' => $price,
                'action' => 'sell',
                'amount' => $amount,
                'total' => $totalEarnings
            ];
            $this->logOrder($order);
            echo "Sold $amount of $shortName at $$price each for a total of $$totalEarnings.\n";
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }


    private function logOrder(object $order) {
        $orders = [];
        if (file_exists('orders.json')) {
            $orders = json_decode(file_get_contents('orders.json'));
        }
        $orders[] = $order;
        file_put_contents('orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    }

    public function displayTransactions() {
        if (file_exists('orders.json')) {
            $orders = json_decode(file_get_contents('orders.json'));
            $table = new ConsoleTable();
            $table->setHeaders(['Time', 'Crypto', 'Price', 'Action', 'Amount', 'Total']);
            foreach ($orders as $order) {
                $table->addRow([
                    $order->time,
                    $order->crypto,
                    '$' . number_format($order->price, 2),
                    $order->action,
                    $order->amount,
                    '$' . number_format($order->total, 2)
                ]);
            }
            $table->display();
        } else {
            echo "No transactions found.\n";
        }
    }
}

$app = new CryptoApp();

echo "WELCOME TO CRYPTO TRADE APP\n";
while (true) {
    echo "1. Check balance\n";
    echo "2. Display 10 trending cryptos\n";
    echo "3. Search for a Crypto\n";
    echo "4. Buy Crypto\n";
    echo "5. Sell Crypto\n";
    echo "6. Display all transactions\n";
    echo "7. Exit\n";

    $choice = readline("Enter your choice: ");
    switch ($choice) {
        case 1:
            $app->checkBalance();
            break;
        case 2:
            $app->displayTopTrendingCryptos();
            break;
        case 3:
            $shortName = readline("Enter the cryptocurrency symbol (e.g., ETH, BTC): ");
            $app->searchCrypto(strtoupper($shortName));
            break;
        case 4:
            $shortName = readline("Enter the cryptocurrency symbol (e.g., ETH, BTC): ");
            $app->buyCrypto(strtoupper($shortName));
            break;
        case 5:
            $shortName = readline("Enter the cryptocurrency symbol (e.g., ETH, BTC): ");
            $app->sellCrypto(strtoupper($shortName));
            break;

        case 6:
            $app->displayTransactions();
            break;
        case 7:
            exit("Exiting...\n");
        default:
            echo "Invalid choice. Please try again.\n";
    }
}
