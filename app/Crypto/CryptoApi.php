<?php

namespace Crypto;

interface CryptoApi
{
    public function getCryptoPrice(string $symbol): float;
    public function getTopCryptos(): array;
    public function searchCrypto(string $symbol, array $cryptos);
    public function displayTopCryptos(array $cryptos);
}
