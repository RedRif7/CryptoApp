<?php

namespace Wallet;

use User\User;
use Transactions\Transactions;
use Crypto\CryptoApi;

class Wallet
{
    private User $user;
    private Transactions $transactions;
    private CryptoApi $cryptoApi;
    private $userFile;

    public function __construct(User $user, Transactions $transactions, CryptoApi $cryptoApi)
    {
        $this->user = $user;
        $this->transactions = $transactions;
        $this->cryptoApi = $cryptoApi;
        $this->userFile = __DIR__ . '/../User/Data/users.json';
    }

    private function loadUserData(): array
    {
        return json_decode(file_get_contents($this->userFile), true);
    }

    private function saveUserData(array $users)
    {
        file_put_contents($this->userFile, json_encode($users));
    }

    public function buyCrypto(string $symbol, float $amount)
    {
        $user = $_SESSION['user'];
        $users = $this->loadUserData();
        $cryptoPrice = $this->cryptoApi->getCryptoPrice($symbol);

        if (!isset($users[$user]['balance']) || $users[$user]['balance'] < $cryptoPrice * $amount) {
            throw new \Exception('Insufficient funds');
        }

        $users[$user]['balance'] -= $cryptoPrice * $amount;
        if (!isset($users[$user]['cryptoBalance'][$symbol])) {
            $users[$user]['cryptoBalance'][$symbol] = 0;
        }
        $users[$user]['cryptoBalance'][$symbol] += $amount;
        $this->transactions->addTransaction($user, 'buy', $symbol, $amount, $cryptoPrice);

        $this->saveUserData($users);
    }

    public function sellCrypto(string $symbol, float $amount)
    {
        $user = $_SESSION['user'];
        $users = $this->loadUserData();
        $cryptoPrice = $this->cryptoApi->getCryptoPrice($symbol);

        if (!isset($users[$user]['cryptoBalance'][$symbol]) || $users[$user]['cryptoBalance'][$symbol] < $amount) {
            throw new \Exception('Insufficient crypto balance');
        }

        $users[$user]['cryptoBalance'][$symbol] -= $amount;
        $users[$user]['balance'] += $cryptoPrice * $amount;
        $this->transactions->addTransaction($user, 'sell', $symbol, $amount, $cryptoPrice);

        $this->saveUserData($users);
    }

    public function getBalance(): float
    {
        $user = $_SESSION['user'];
        $users = $this->loadUserData();
        return $users[$user]['balance'] ?? 0.0;
    }

    public function getCryptoBalance(): array
    {
        $user = $_SESSION['user'];
        $users = $this->loadUserData();
        return $users[$user]['cryptoBalance'] ?? [];
    }
}
