<?php

require 'vendor/autoload.php';

use User\User;
use Wallet\Wallet;
use Transactions\Transactions;
use Crypto\CoinMarketCapApi;
use Crypto\Crypto;

echo "Welcome to the Crypto Trading CLI App!\n";

$users = [
    ['name' => 'kristaps', 'password' => md5('password')],
    ['name' => 'alise', 'password' => md5('password')],
];

echo "Please log in.\n";
echo "Username: ";
$username = trim(fgets(STDIN));
echo "Password: ";
$password = trim(fgets(STDIN));

$loggedInUser = null;

foreach ($users as $user) {
    if ($user['name'] === $username && $user['password'] === md5($password)) {
        $loggedInUser = new User($username);
        break;
    }
}

if (!$loggedInUser) {
    echo "Invalid username or password.\n";
    exit(1);
}

$transactions = new Transactions();
$cryptoApi = new CoinMarketCapApi();
$wallet = new Wallet($loggedInUser, $transactions, $cryptoApi);
$crypto = new Crypto($cryptoApi);

while (true) {
    echo "\nMenu:\n";
    echo "1. Display Balance\n";
    echo "2. Display Transactions\n";
    echo "3. Buy Crypto\n";
    echo "4. Sell Crypto\n";
    echo "5. Search Crypto\n";
    echo "6. Display Top Cryptos\n";
    echo "7. Exit\n";
    echo "Choose an option: ";
    $option = trim(fgets(STDIN));

    switch ($option) {
        case 1:
            $loggedInUser->displayUserBalance($cryptoApi);
            break;
        case 2:
            $transactions->displayUserTransactions($username);
            break;
        case 3:
            echo "Enter the symbol of the crypto to buy: ";
            $symbol = trim(fgets(STDIN));
            echo "Enter the amount to buy: ";
            $amount = floatval(trim(fgets(STDIN)));
            $wallet->buyCrypto($symbol, $amount);
            break;
        case 4:
            echo "Enter the symbol of the crypto to sell: ";
            $symbol = trim(fgets(STDIN));
            echo "Enter the amount to sell: ";
            $amount = floatval(trim(fgets(STDIN)));
            $wallet->sellCrypto($symbol, $amount);
            break;
        case 5:
            echo "Enter the symbol of the crypto to search: ";
            $symbol = trim(fgets(STDIN));
            $crypto->searchCrypto($symbol);
            break;
        case 6:
            $crypto->displayTopCryptos();
            break;
        case 7:
            echo "Goodbye!\n";
            exit(0);
        default:
            echo "Invalid option. Please try again.\n";
    }
}
