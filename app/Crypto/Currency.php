<?php

namespace Crypto;

class Currency
{
    private string $name;
    private string $symbol;
    private float $price;
    private float $marketCap;
    private float $volume24h;

    public function __construct(string $name, string $symbol, float $price, float $marketCap, float $volume24h)
    {
        $this->name = $name;
        $this->symbol = $symbol;
        $this->price = $price;
        $this->marketCap = $marketCap;
        $this->volume24h = $volume24h;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSymbol(): string {
        return $this->symbol;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getMarketCap(): float {
        return $this->marketCap;
    }

    public function getVolume24h(): float {
        return $this->volume24h;
    }
}
