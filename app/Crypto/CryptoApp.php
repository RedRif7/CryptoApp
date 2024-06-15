<?php

namespace Crypto;

use User\User;
use Transactions\Transactions;
use Exception;

class CryptoApp
{
    private CryptoApi $cryptoApi;
    private User $user;
    private Transactions $transactions;

    public function __construct(CryptoApi $cryptoApi, User $user, Transactions $transactions)
    {
        $this->cryptoApi = $cryptoApi;
        $this->user = $user;
        $this->transactions = $transactions;
    }

    public function displayTopCryptos()
    {
        try {
            $cryptos = $this->cryptoApi->getTopCryptos();
            foreach ($cryptos as $crypto) {
                echo "ID: " . $crypto->id . "\n";
                echo "Name: " . $crypto->name . "\n";
                echo "Symbol: " . $crypto->symbol . "\n";
                echo "Price: $" . number_format($crypto->price, 2) . "\n";
                echo "-------------------------\n";
            }
        } catch (Exception $e) {
            throw new Exception("Failed to display top cryptocurrencies: " . $e->getMessage());
        }
    }

    public function buyCrypto(string $symbol, float $amount)
    {
        try {
            $price = $this->cryptoApi->getCryptoPrice($symbol);
            $totalCost = $price * $amount;
            if ($this->user->getBalance() >= $totalCost) {
                $this->user->updateBalance(-$totalCost);
                $this->user->updateCryptoBalance($symbol, $amount);
                $this->transactions->logTransaction('buy', $symbol, $price, $amount);
                echo "Bought $amount of $symbol at $$price each. Total cost: $$totalCost\n";
            } else {
                echo "Insufficient balance to buy $amount of $symbol.\n";
            }
        } catch (Exception $e) {
            throw new Exception("Failed to buy cryptocurrency: " . $e->getMessage());
        }
    }

    public function sellCrypto(string $symbol, float $amount)
    {
        try {
            $price = $this->cryptoApi->getCryptoPrice($symbol);
            if (isset($this->user->getCryptoBalance()[$symbol]) && $this->user->getCryptoBalance()[$symbol] >= $amount) {
                $totalRevenue = $price * $amount;
                $this->user->updateBalance($totalRevenue);
                $this->user->updateCryptoBalance($symbol, -$amount);
                $this->transactions->logTransaction('sell', $symbol, $price, $amount);
                echo "Sold $amount of $symbol at $$price each. Total revenue: $$totalRevenue\n";
            } else {
                echo "Insufficient $symbol balance to sell.\n";
            }
        } catch (Exception $e) {
            throw new Exception("Failed to sell cryptocurrency: " . $e->getMessage());
        }
    }

    public function searchCrypto(string $symbol)
    {
        try {
            $crypto = $this->cryptoApi->getCryptoBySymbol($symbol);
            if ($crypto) {
                echo "ID: " . $crypto->id . "\n";
                echo "Name: " . $crypto->name . "\n";
                echo "Symbol: " . $crypto->symbol . "\n";
                echo "Price: $" . number_format($crypto->price, 2) . "\n";
            } else {
                echo "Cryptocurrency with symbol '$symbol' not found.\n";
            }
        } catch (Exception $e) {
            throw new Exception("Failed to search for cryptocurrency: " . $e->getMessage());
        }
    }

    public function displayBalance()
    {
        $this->user->displayBalance();
    }
}
