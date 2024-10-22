<?php

declare(strict_types=1);

namespace App\Domain\VO;

final class Money
{
    public function __construct(
        private readonly float $amount,
        private readonly MoneyCurrency $currency,
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): MoneyCurrency
    {
        return $this->currency;
    }

    public function hasSameCurrency(Money $otherMoney): bool
    {
        return $this->currency->isEqual($otherMoney->getCurrency());
    }
}
