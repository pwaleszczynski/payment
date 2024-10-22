<?php

declare(strict_types=1);

namespace App\Domain\Model\Operation;

use App\Domain\VO\Money;
use DateTimeImmutable;

final class WithCommissionDebitOperation implements DebitOperation
{
    private const COMMISSION = 0.05;

    private DateTimeImmutable $date;

    public function __construct(
        private Money $debitMoney,
    ){
        $this->date = new DateTimeImmutable();
    }

    public function getMoney(): Money
    {
        return new Money(
            $this->debitMoney->getAmount() + ($this->debitMoney->getAmount() * self::COMMISSION),
            $this->debitMoney->getCurrency(),
        );
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
