<?php

namespace User;

use Medoo\Medoo;
use Exception;

class User
{
    private Medoo $database;
    private float $balance;
    private string $name;
    private array $cryptoBalance;

    public function __construct(Medoo $database)
    {
        $this->database = $database;
        $this->loadUser();
    }

    private function loadUser()
    {
        try {
            $userData = $this->database->get('user', '*', ['id' => 1]);
            if ($userData) {
                $this->balance = (float)$userData['balance'];
                $this->name = $userData['name'];
                $this->cryptoBalance = json_decode($userData['crypto_balance'], true);
            } else {
                $this->balance = 1000.00;
                $this->name = 'Default User';
                $this->cryptoBalance = [];
                $this->saveUser();
            }
        } catch (Exception $e) {
            throw new Exception("Failed to load user: " . $e->getMessage());
        }
    }

    public function saveUser()
    {
        try {
            $this->database->replace('user', [
                'id' => 1,
                'balance' => $this->balance,
                'name' => $this->name,
                'crypto_balance' => json_encode($this->cryptoBalance)
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to save user: " . $e->getMessage());
        }
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCryptoBalance(): array
    {
        return $this->cryptoBalance;
    }

    public function updateBalance(float $amount)
    {
        $this->balance += $amount;
        $this->saveUser();
    }

    public function updateCryptoBalance(string $symbol, float $amount)
    {
        if (!isset($this->cryptoBalance[$symbol])) {
            $this->cryptoBalance[$symbol] = 0;
        }
        $this->cryptoBalance[$symbol] += $amount;
        $this->saveUser();
    }

    public function displayBalance()
    {
        echo "Balance: $" . number_format($this->balance, 2) . "\n";
        echo "Cryptocurrencies:\n";
        foreach ($this->cryptoBalance as $symbol => $amount) {
            echo "$symbol: $amount\n";
        }
    }
}
