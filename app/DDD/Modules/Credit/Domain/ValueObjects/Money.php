<?php

namespace App\DDD\Modules\Credit\Domain\ValueObjects;

class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'TRY')
    {
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $money): Money
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \InvalidArgumentException('Farklı para birimleri toplanamaz');
        }

        return new Money($this->amount + $money->getAmount(), $this->currency);
    }

    public function subtract(Money $money): Money
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \InvalidArgumentException('Farklı para birimleri çıkarılamaz');
        }

        return new Money($this->amount - $money->getAmount(), $this->currency);
    }

    public function multiply(float $multiplier): Money
    {
        return new Money($this->amount * $multiplier, $this->currency);
    }

    public function isGreaterThan(Money $money): bool
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \InvalidArgumentException('Farklı para birimleri karşılaştırılamaz');
        }

        return $this->amount > $money->getAmount();
    }

    public function isLessThan(Money $money): bool
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \InvalidArgumentException('Farklı para birimleri karşılaştırılamaz');
        }

        return $this->amount < $money->getAmount();
    }

    public function equals(Money $money): bool
    {
        return $this->currency === $money->getCurrency() && $this->amount === $money->getAmount();
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
} 