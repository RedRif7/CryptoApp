<?php

namespace Crypto;

use LucidFrame\Console\ConsoleTable;
use Crypto\CryptoApi;

class Crypto
{
    private CryptoApi $cryptoApi;

    public function __construct(CryptoApi $cryptoApi)
    {
        $this->cryptoApi = $cryptoApi;
    }

    public function displayTopCryptos()
    {
        $cryptos = $this->cryptoApi->getTopCryptos();
        $this->cryptoApi->displayTopCryptos($cryptos);
    }

    public function searchCrypto(string $symbol)
    {
        $cryptos = $this->cryptoApi->getTopCryptos();
        $this->cryptoApi->searchCrypto($symbol, $cryptos);
    }
}
