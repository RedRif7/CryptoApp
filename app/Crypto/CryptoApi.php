<?php

namespace Crypto;

interface CryptoApi
{
    public function getTopCryptos(): array;

    public function getCryptoPrice(string $symbol): ?float;

    public function getCryptoBySymbol(string $symbol): ?object;

}
