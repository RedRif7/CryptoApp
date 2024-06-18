<?php

namespace Wallet;

use User\User;
use Crypto\CryptoApi;
use Transactions\Transactions;

class Wallet
{
    private User $user;
    private Transactions $transactions;
    private CryptoApi $cryptoApi;

    public function __construct(User $user, Transactions $transactions, CryptoApi $cryptoApi)
    {
        $this->user = $user;
        $this->transactions = $transactions;
        $this->cryptoApi = $cryptoApi;
    }

    public function buyCrypto(string $symbol, float $amount)
    {
        try {
            $price = $this->cryptoApi->getCryptoPrice($symbol);
            $cost = $price * $amount;

            if ($cost > $this->user->getBalance()) {
                echo "Insufficient funds to complete the transaction.\n";
                return;
            }

            $this->user->updateBalance(-$cost);
            $this->user->updateCryptoBalance($symbol, $amount);

            $transaction = [
                'user' => $this->user->getName(),
                'type' => 'buy',
                'symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'date' => date('Y-m-d H:i:s')
            ];
            $this->transactions->recordTransaction($transaction);

            echo "Bought $amount of $symbol at \$$price each. Total cost: \$$cost.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    public function sellCrypto(string $symbol, float $amount)
    {
        try {
            $price = $this->cryptoApi->getCryptoPrice($symbol);
            $value = $price * $amount;

            if (!isset($this->user->getCryptoBalance()[$symbol]) || $this->user->getCryptoBalance()[$symbol] < $amount) {
                echo "Insufficient crypto balance to complete the transaction.\n";
                return;
            }

            $this->user->updateBalance($value);
            $this->user->updateCryptoBalance($symbol, -$amount);

            $transaction = [
                'user' => $this->user->getName(),
                'type' => 'sell',
                'symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'date' => date('Y-m-d H:i:s')
            ];
            $this->transactions->recordTransaction($transaction);

            echo "Sold $amount of $symbol at \$$price each. Total value: \$$value.\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
