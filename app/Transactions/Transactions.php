<?php

namespace Transactions;

use Carbon\Carbon;
use User\User;
use LucidFrame\Console\ConsoleTable;

class Transactions {
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    private function saveTransaction(string $symbol, float $amount, float $price, string $type) {
        $transactions = json_decode(file_get_contents(__DIR__ . '/Data/transactions.json'), false);

        $transaction = [
            'time' => Carbon::now()->toDateTimeString(),
            'symbol' => $symbol,
            'amount' => $amount,
            'price' => $price,
            'type' => $type,
            'total' => $amount * $price
        ];

        $transactions[] = $transaction;
        file_put_contents(__DIR__ . '/Data/transactions.json', json_encode($transactions, JSON_PRETTY_PRINT));
    }

    public function buyCrypto(string $symbol, float $price, float $amount) {
        $totalCost = $amount * $price;
        if ($this->user->getBalance() < $totalCost) {
            echo "Insufficient balance.\n";
            return;
        }

        $this->user->updateBalance(-$totalCost);
        $this->user->updateCryptoBalance($symbol, $amount);
        $this->saveTransaction($symbol, $amount, $price, 'buy');
        echo "Bought $amount of $symbol at $price each.\n";
    }

    public function sellCrypto(string $symbol, float $price, float $amount) {
        $cryptoBalance = $this->user->getCryptoBalance();
        if (!isset($cryptoBalance[$symbol]) || $cryptoBalance[$symbol] < $amount) {
            echo "Insufficient cryptocurrency balance.\n";
            return;
        }

        $totalProceeds = $amount * $price;
        $this->user->updateBalance($totalProceeds);
        $this->user->updateCryptoBalance($symbol, -$amount);
        $this->saveTransaction($symbol, $amount, $price, 'sell');
        echo "Sold $amount of $symbol at $price each.\n";
    }

    public function displayOrders() {
        $transactions = json_decode(file_get_contents(__DIR__ . '/Data/transactions.json'), false);
        if (empty($transactions)) {
            echo "No transactions found.\n";
            return;
        }

        $table = new ConsoleTable();
        $table->setHeaders(['Time', 'Symbol', 'Amount', 'Price', 'Type', 'Total']);
        foreach ($transactions as $transaction) {
            $table->addRow([
                $transaction->time,
                $transaction->symbol,
                $transaction->amount,
                "$".number_format($transaction->price,5),
                $transaction->type,
                "$".$transaction->total
            ]);
        }
        $table->display();
    }
}
