<?php

namespace Transactions;

use LucidFrame\Console\ConsoleTable;

class Transactions
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/Data/transactions.json';
    }

    public function displayUserTransactions(string $userName)
    {
        $transactionsData = $this->getAllTransactions();

        $userTransactions = array_filter($transactionsData, function ($transaction) use ($userName) {
            return $transaction['user'] === $userName;
        });

        if (empty($userTransactions)) {
            echo "No transactions found for user $userName.\n";
            return;
        }

        $table = new ConsoleTable();
        $table->setHeaders(['Date', 'Type', 'Symbol', 'Amount', 'Price']);

        foreach ($userTransactions as $transaction) {
            $table->addRow([
                $transaction['date'],
                $transaction['type'],
                $transaction['symbol'],
                $transaction['amount'],
                '$' . number_format($transaction['price'], 2)
            ]);
        }

        echo "Transactions for $userName:\n";
        $table->display();
    }

    public function recordTransaction(array $transaction)
    {
        $transactions = $this->getAllTransactions();
        $transactions[] = $transaction;
        $this->saveTransactions($transactions);
    }

    private function getAllTransactions(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }
        return json_decode(file_get_contents($this->filePath), true);
    }

    private function saveTransactions(array $transactions): void
    {
        file_put_contents($this->filePath, json_encode($transactions, JSON_PRETTY_PRINT));
    }
}
