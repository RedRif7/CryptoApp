<?php

require 'vendor/autoload.php';
require 'app/User/User.php';
require 'app/Transactions/Transactions.php';
require 'app/Crypto/CryptoApi.php';
require 'app/Crypto/CoinMarketCapApi.php';
require 'app/Crypto/Currency.php';
require 'app/Crypto/CryptoApp.php';
require 'app/Database/DatabaseInitializer.php';

use Medoo\Medoo;
use User\User;
use Crypto\CoinMarketCapApi;
use Crypto\CryptoApp;
use Transactions\Transactions;
use Database\DatabaseInitializer;

try {
    $database = new Medoo([
        'database_type' => 'sqlite',
        'database_file' => 'app/CryptoApp/app.sqlite'
    ]);

    // Initialize database
    $initializer = new DatabaseInitializer($database);
    $initializer->initialize();

    $user = new User($database);
    $transactions = new Transactions($database, $user);
    $cryptoApi = new CoinMarketCapApi();
    $app = new CryptoApp($cryptoApi, $user, $transactions);

    echo "Welcome, " . $user->getName() . "!\n";

    while (true) {
        echo "\nMenu:\n";
        echo "1. Display Top Cryptocurrencies\n";
        echo "2. Buy Cryptocurrency\n";
        echo "3. Sell Cryptocurrency\n";
        echo "4. Display Transactions\n";
        echo "5. Search Cryptocurrency\n";
        echo "6. Display Balance\n";
        echo "7. Exit\n";
        $choice = readline("Choose an option: ");

        switch ($choice) {
            case 1:
                $app->displayTopCryptos();
                break;
            case 2:
                $crypto = readline("Enter the cryptocurrency symbol to buy: ");
                $amount = readline("Enter the amount to buy: ");
                $app->buyCrypto($crypto, (float)$amount);
                break;
            case 3:
                $crypto = readline("Enter the cryptocurrency symbol to sell: ");
                $amount = readline("Enter the amount to sell: ");
                $app->sellCrypto($crypto, (float)$amount);
                break;
            case 4:
                $app->getTransactions()->displayOrders();
                break;
            case 5:
                $crypto = readline("Enter the cryptocurrency symbol to search: ");
                $app->searchCrypto($crypto);
                break;
            case 6:
                $user->displayBalance();
                break;
            case 7:
                echo "Exiting CryptoApp. Goodbye!\n";
                exit;
            default:
                echo "Invalid option. Please choose a number from 1 to 7.\n";
        }
    }
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}
