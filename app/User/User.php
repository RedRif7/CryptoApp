<?php

namespace User;

use LucidFrame\Console\ConsoleTable;
use Crypto\CryptoApi;

class User
{
    private string $name;
    private float $balance;
    private array $cryptoBalance;
    private string $filePath;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->filePath = __DIR__ . '/../Data/users.json';
        $this->loadUserData();
    }

    private function loadUserData(): void
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['name'] === $this->name) {
                $this->balance = $user['balance'];
                $this->cryptoBalance = $user['cryptoBalance'];
                return;
            }
        }

        // If user does not exist, initialize with default values
        $this->balance = 1000.0;
        $this->cryptoBalance = [];
        $this->saveUserData();
    }

    private function saveUserData(): void
    {
        $users = $this->getAllUsers();
        $updated = false;
        foreach ($users as &$user) {
            if ($user['name'] === $this->name) {
                $user['balance'] = $this->balance;
                $user['cryptoBalance'] = $this->cryptoBalance;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            $users[] = [
                'name' => $this->name,
                'balance' => $this->balance,
                'cryptoBalance' => $this->cryptoBalance
            ];
        }

        file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function getAllUsers(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }
        return json_decode(file_get_contents($this->filePath), true);
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getCryptoBalance(): array
    {
        return $this->cryptoBalance;
    }

    public function updateBalance(float $amount): void
    {
        $this->balance += $amount;
        $this->saveUserData();
    }

    public function updateCryptoBalance(string $symbol, float $amount): void
    {
        if (!isset($this->cryptoBalance[$symbol])) {
            $this->cryptoBalance[$symbol] = 0;
        }
        $this->cryptoBalance[$symbol] += $amount;
        $this->saveUserData();
    }

    public function displayUserBalance(CryptoApi $cryptoApi)
    {
        $table = new ConsoleTable();
        $table->setHeaders(['Category', 'Amount']);

        $table->addRow(['User', $this->name]);
        $table->addRow(['Balance', '$' . number_format($this->balance, 2)]);

        foreach ($this->cryptoBalance as $symbol => $amount) {
            $price = $cryptoApi->getCryptoPrice($symbol);
            $value = $amount * $price;
            $table->addRow(["Crypto ($symbol)", "$amount ($" . number_format($value, 2) . ")"]);
        }

        echo "User Balance and Crypto Holdings:\n";
        $table->display();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
