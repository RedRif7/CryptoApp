<?php

require 'vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Crypto\CoinMarketCapApi;
use User\User;
use Wallet\Wallet;
use Transactions\Transactions;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$coinMarketCapApi = new CoinMarketCapApi();
$transactions = new Transactions();
$user = new User();
$wallet = new Wallet($user, $transactions, $coinMarketCapApi);

session_start();

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', 'displayTopCryptos');
    $r->addRoute('GET', '/search', 'searchCrypto');
    $r->addRoute('GET', '/transactions', 'displayTransactions');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($handler) {
            case 'displayTopCryptos':
                $cryptos = $coinMarketCapApi->getTopCryptos();
                echo $twig->render('top_currencies.twig', ['cryptos' => $cryptos]);
                break;

            case 'searchCrypto':
                $symbol = $_GET['symbol'] ?? '';
                $crypto = $coinMarketCapApi->getCryptoBySymbol($symbol, $coinMarketCapApi->getTopCryptos());
                echo $twig->render('search_result.twig', ['crypto' => $crypto]);
                break;

            case 'displayTransactions':
                $transactionsList = $transactions->getTransactions();
                echo $twig->render('transactions.twig', ['transactions' => $transactionsList]);
                break;

            default:
                echo '404 Not Found';
                break;
        }
        break;
}
