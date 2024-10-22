<?php

declare(strict_types=1);

namespace App\Domain\Model\Operation;

use App\Domain\VO\Money;

final class CreditOperation
{
    public function __construct(
        private readonly Money $money,
    ) {
    }

    public function getAmount(): float
    {
        return $this->money->getAmount();
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
