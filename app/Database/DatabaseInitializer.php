<?php

namespace Database;

use Medoo\Medoo;
use Exception;

class DatabaseInitializer
{
    private Medoo $database;

    public function __construct(Medoo $database)
    {
        $this->database = $database;
    }

    public function initialize()
    {
        try {
            $this->createUserTable();
            $this->createTransactionsTable();
        } catch (Exception $e) {
            throw new Exception("Database initialization failed: " . $e->getMessage());
        }
    }

    private function createUserTable()
    {
        try {
            $tableCheck = $this->database->query("SELECT name FROM sqlite_master WHERE type='table' AND name='user'")->fetch();
            if (!$tableCheck) {
                $this->database->query("
                    CREATE TABLE user (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        balance REAL NOT NULL,
                        name TEXT NOT NULL,
                        crypto_balance TEXT NOT NULL
                    )
                ");
            }
        } catch (Exception $e) {
            throw new Exception("User table creation failed: " . $e->getMessage());
        }
    }

    private function createTransactionsTable()
    {
        try {
            $tableCheck = $this->database->query("SELECT name FROM sqlite_master WHERE type='table' AND name='transactions'")->fetch();
            if (!$tableCheck) {
                $this->database->query("
                    CREATE TABLE transactions (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        type TEXT NOT NULL,
                        symbol TEXT NOT NULL,
                        price REAL NOT NULL,
                        amount REAL NOT NULL,
                        timestamp TEXT NOT NULL
                    )
                ");
            }
        } catch (Exception $e) {
            throw new Exception("Transactions table creation failed: " . $e->getMessage());
        }
    }
}
