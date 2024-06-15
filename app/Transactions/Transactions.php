<?php

namespace Transactions;

use Medoo\Medoo;
use User\User;
use Exception;

class Transactions
{
    private Medoo $database;
    private User $user;

    public function __construct(Medoo $database, User $user)
    {
        $this->database = $database;
        $this->user = $user;
    }

    public function logTransaction(string $type, string $symbol, float $price, float $amount)
    {
        try {
            $this->database->insert('transactions', [
                'type' => $type,
                'symbol' => $symbol,
                'price' => $price,
                'amount' => $amount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to log transaction: " . $e->getMessage());
        }
    }

    public function displayOrders()
    {
        try {
            $orders = $this->database->select('transactions', '*');
            if ($orders) {
                foreach ($orders as $order) {
                    echo "ID: " . $order['id'] . "\n";
                    echo "Type: " . $order['type'] . "\n";
                    echo "Symbol: " . $order['symbol'] . "\n";
                    echo "Price: " . $order['price'] . "\n";
                    echo "Amount: " . $order['amount'] . "\n";
                    echo "Timestamp: " . $order['timestamp'] . "\n";
                    echo "-------------------------\n";
                }
            } else {
                echo "No transactions found.\n";
            }
        } catch (Exception $e) {
            throw new Exception("Failed to display orders: " . $e->getMessage());
        }
    }
}
