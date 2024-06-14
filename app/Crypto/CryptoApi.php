<?php

namespace Crypto;

interface CryptoApi
{
    public function getTopCryptos(int $limit = 10): array;
    public function getCryptoPrice(string $symbol): ?float;
    public function searchCrypto(string $symbol): ?Currency;
}
