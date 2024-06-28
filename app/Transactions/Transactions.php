<?php

namespace Transactions;

class Transactions
{
    private $transactionsFile;

    public function __construct()
    {
        $this->transactionsFile = __DIR__ . '/Data/transactions.json';
        if (!file_exists($this->transactionsFile)) {
            file_put_contents($this->transactionsFile, json_encode([]));
        }
    }

    public function addTransaction(string $user, string $type, string $symbol, float $amount, float $price)
    {
        $transactions = json_decode(file_get_contents($this->transactionsFile), true);
        $transactions[] = [
            'user' => $user,
            'type' => $type,
            'symbol' => $symbol,
            'amount' => $amount,
            'price' => $price,
            'date' => date('Y-m-d H:i:s')
        ];
        file_put_contents($this->transactionsFile, json_encode($transactions));
    }

    public function getTransactions(): array
    {
        return json_decode(file_get_contents($this->transactionsFile), true);
    }
}
