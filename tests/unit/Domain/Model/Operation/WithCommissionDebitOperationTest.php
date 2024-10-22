<?php

declare(strict_types=1);

namespace Tests\unit\Domain\Model\Operation;

use App\Domain\Model\Operation\WithCommissionDebitOperation;
use App\Domain\VO\Money;
use App\Domain\VO\MoneyCurrency;
use PHPUnit\Framework\TestCase;

final class WithCommissionDebitOperationTest extends TestCase
{
    public function testShouldCalculateMoneyCorrectly(): void
    {
        $money = new Money(20, new MoneyCurrency('PLN'));

        $withCommissionDebitOperation = new WithCommissionDebitOperation($money);

        self::assertSame('PLN', $withCommissionDebitOperation->getMoney()->getCurrency()->getCode());
        self::assertSame(21.0, $withCommissionDebitOperation->getMoney()->getAmount());
    }
}
