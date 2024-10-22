<?php

declare(strict_types=1);

namespace Tests\unit\Domain\Model;

use App\Domain\Event\DebitOperationWasCalledEvent;
use App\Domain\Exception\InsufficientFundsException;
use App\Domain\Exception\InvalidOperationCurrencyException;
use App\Domain\Exception\NotAllowedDailyDebitOperationCallException;
use App\Domain\Model\BankAccount;
use App\Domain\Model\Operation\CreditOperation;
use App\Domain\Model\Operation\DebitOperation;
use App\Domain\Specification\AllowedDailyDebitOperationCallSpecification;
use App\Domain\VO\Money;
use App\Domain\VO\MoneyCurrency;
use PHPUnit\Framework\TestCase;

final class BankAccountTest extends TestCase
{
    public function testShouldNotPerformCreditOperationWithInvalidCurrency(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $creditOperationMoney = new Money(10, new MoneyCurrency('USD'));
        $bankAccount = new BankAccount($bankAccountMoney);

        $this->expectException(InvalidOperationCurrencyException::class);

        $bankAccount->credit(new CreditOperation($creditOperationMoney));
    }

    public function testShouldNotPerformDebitOperationWithInvalidCurrency(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $debitOperationMoney = new Money(10, new MoneyCurrency('USD'));
        $bankAccount = new BankAccount($bankAccountMoney);
        $debitOperation = $this->createMock(DebitOperation::class);
        $debitOperation
            ->method('getMoney')
            ->willReturn($debitOperationMoney);
        $allowedDailyDebitOperationCallSpecification =
            $this->createMock(AllowedDailyDebitOperationCallSpecification::class);
        $allowedDailyDebitOperationCallSpecification->method('isAllowed')->willReturn(true);

        $this->expectException(InvalidOperationCurrencyException::class);

        $bankAccount->debit($debitOperation, $allowedDailyDebitOperationCallSpecification);
    }

    public function testShouldNotPerformDebitOperationWithInsufficientFunds(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $debitOperationMoney = new Money(100, new MoneyCurrency('PLN'));
        $debitOperation = $this->createMock(DebitOperation::class);
        $debitOperation
            ->method('getMoney')
            ->willReturn($debitOperationMoney);
        $allowedDailyDebitOperationCallSpecification =
            $this->createMock(AllowedDailyDebitOperationCallSpecification::class);
        $allowedDailyDebitOperationCallSpecification->method('isAllowed')->willReturn(true);
        $bankAccount = new BankAccount($bankAccountMoney);

        $this->expectException(InsufficientFundsException::class);

        $bankAccount->debit($debitOperation, $allowedDailyDebitOperationCallSpecification);
    }

    public function testShouldNotAllowDebitOperationForInvalidDailyAll(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $debitOperationMoney = new Money(5, new MoneyCurrency('PLN'));
        $debitOperation = $this->createMock(DebitOperation::class);
        $debitOperation
            ->method('getMoney')
            ->willReturn($debitOperationMoney);
        $allowedDailyDebitOperationCallSpecification =
            $this->createMock(AllowedDailyDebitOperationCallSpecification::class);
        $allowedDailyDebitOperationCallSpecification->method('isAllowed')->willReturn(false);
        $bankAccount = new BankAccount($bankAccountMoney);

        $this->expectException(NotAllowedDailyDebitOperationCallException::class);

        $bankAccount->debit($debitOperation, $allowedDailyDebitOperationCallSpecification);
    }

    public function testShouldPerformCreditOperationCorrectly(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $creditOperationMoney = new Money(10, new MoneyCurrency('PLN'));
        $bankAccount = new BankAccount($bankAccountMoney);

        $bankAccount->credit(new CreditOperation($creditOperationMoney));

        self::assertSame(30.0, $bankAccount->getAmount());
        self::assertSame('PLN', $bankAccount->getCurrency()->getCode());
    }

    public function testShouldPerformDebitOperationCorrectly(): void
    {
        $bankAccountMoney = new Money(20, new MoneyCurrency('PLN'));
        $debitOperationMoney = new Money(5, new MoneyCurrency('PLN'));
        $debitOperationDate = new \DateTimeImmutable();
        $debitOperation = $this->createMock(DebitOperation::class);
        $debitOperation
            ->method('getMoney')
            ->willReturn($debitOperationMoney);
        $debitOperation->method('getDate')->willReturn($debitOperationDate);
        $allowedDailyDebitOperationCallSpecification =
            $this->createMock(AllowedDailyDebitOperationCallSpecification::class);
        $allowedDailyDebitOperationCallSpecification->method('isAllowed')->willReturn(true);
        $bankAccount = new BankAccount($bankAccountMoney);

        $bankAccount->debit($debitOperation, $allowedDailyDebitOperationCallSpecification);

        $events = $bankAccount->getRecordedEvents();

        self::assertSame(15.0, $bankAccount->getAmount());
        self::assertSame('PLN', $bankAccount->getCurrency()->getCode());
        self::assertCount(1, $events);
        self::assertInstanceOf(DebitOperationWasCalledEvent::class, $events[0]);
    }

}
