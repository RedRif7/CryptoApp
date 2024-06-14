<?php

namespace User;

class User
{
    private float $balance;
    private string $name;
    private array $cryptoBalance;

    public function __construct(float $balance, string $name, array $cryptoBalance) {
        $this->balance = $balance;
        $this->name = $name;
        $this->cryptoBalance = $cryptoBalance;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCryptoBalance(): array {
        return $this->cryptoBalance;
    }

    public function updateBalance(float $amount) {
        $this->balance += $amount;
    }

    public function updateCryptoBalance(string $symbol, float $amount) {
        if (isset($this->cryptoBalance[$symbol])) {
            $this->cryptoBalance[$symbol] += $amount;
        } else {
            $this->cryptoBalance[$symbol] = $amount;
        }
    }

    public function saveBalance() {
        $data = [
            'balance' => $this->balance,
            'name' => $this->name,
            'cryptoBalance' => $this->cryptoBalance
        ];
        file_put_contents(__DIR__ . '/Data/user.json', json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function loadUser(): User {
        $data = json_decode(file_get_contents(__DIR__ . '/Data/user.json'), true);
        return new User($data['balance'], $data['name'], $data['cryptoBalance']);
    }

    public function displayBalance() {
        echo "User Balance: $" . number_format($this->balance, 2) . "\n";
        echo "Cryptocurrency Holdings:\n";
        foreach ($this->cryptoBalance as $symbol => $amount) {
            echo strtoupper($symbol) . ": " . number_format($amount, 8) . "\n";
        }
    }
}
