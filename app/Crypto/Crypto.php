<?php

namespace Crypto;

use User\User;
use Transactions\Transactions;
use LucidFrame\Console\ConsoleTable;

class Crypto
{
    private CryptoApi $cryptoApi;
    private User $user;
    private Transactions $transactions;

    public function __construct(CryptoApi $cryptoApi, User $user) {
        $this->cryptoApi = $cryptoApi;
        $this->user = $user;
        $this->transactions = new Transactions($user);
    }

    public function displayTopCryptos() {
        $cryptos = $this->cryptoApi->getTopCryptos();
        $table = new ConsoleTable();
        $table->setHeaders(['Name', 'Symbol', 'Price (USD)']);
        foreach ($cryptos as $crypto) {
            $table->addRow([
                $crypto->getName(),
                $crypto->getSymbol(),
                $crypto->getPrice() > 1 ?
                    "$" . number_format($crypto->getPrice(), 2) :
                    "$" . number_format($crypto->getPrice(), 10)

            ]);
        }
        $table->display();
    }

    public function searchCrypto(string $symbol) {
        $crypto = $this->cryptoApi->searchCrypto($symbol);
        if ($crypto) {
            $table = new ConsoleTable();
            $table->setHeaders(['Name', 'Symbol', 'Price (USD)', 'Market Cap', 'Volume (24h)']);
            $table->addRow([
                $crypto->getName(),
                $crypto->getSymbol(),
                "$" . number_format($crypto->getPrice(), 2),
                "$" . number_format($crypto->getMarketCap(), 2),
                "$" . number_format($crypto->getVolume24h(), 2)
            ]);
            $table->display();
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }

    public function displayUserBalance() {
        $this->user->displayBalance();
    }

    public function buyCrypto(string $symbol, float $amount) {
        $price = $this->cryptoApi->getCryptoPrice($symbol);
        if ($price) {
            $this->transactions->buyCrypto($symbol, $price, $amount);
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }

    public function sellCrypto(string $symbol, float $amount) {
        $price = $this->cryptoApi->getCryptoPrice($symbol);
        if ($price) {
            $this->transactions->sellCrypto($symbol, $price, $amount);
        } else {
            echo "Cryptocurrency not found.\n";
        }
    }

    public function getTransactions(): Transactions {
        return $this->transactions;
    }
}
