<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

// SQLite database file path
$databaseFile = __DIR__ . '/crypto_app.db';

// Check if database file exists; if not, create it
if (!file_exists($databaseFile)) {
    // Create SQLite database and tables
    $connectionParams = [
        'path' => $databaseFile,
        'driver' => 'pdo_sqlite',
    ];

    $config = new Configuration();
    $connection = DriverManager::getConnection($connectionParams, $config);

    $schema = $connection->getSchemaManager();

    // Create users table
    $usersTable = new \Doctrine\DBAL\Schema\Table('users');
    $usersTable->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
    $usersTable->addColumn('name', 'string', ['length' => 255]);
    $usersTable->addColumn('balance', 'float');
    $usersTable->setPrimaryKey(['id']);
    $schema->createTable($usersTable);

    // Create transactions table
    $transactionsTable = new \Doctrine\DBAL\Schema\Table('transactions');
    $transactionsTable->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
    $transactionsTable->addColumn('user_id', 'integer');
    $transactionsTable->addColumn('action', 'string', ['length' => 50]);
    $transactionsTable->addColumn('symbol', 'string', ['length' => 50]);
    $transactionsTable->addColumn('amount', 'float');
    $transactionsTable->addColumn('price', 'float');
    $transactionsTable->addColumn('total', 'float');
    $transactionsTable->addColumn('timestamp', 'datetime');
    $transactionsTable->setPrimaryKey(['id']);
    $transactionsTable->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
    $schema->createTable($transactionsTable);

    echo "SQLite database created successfully.\n";
} else {
    echo "SQLite database already exists.\n";
}
