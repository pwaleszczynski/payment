<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Event\DebitOperationWasCalledEvent;
use App\Domain\Event\DomainEvent;
use App\Domain\Exception\InsufficientFundsException;
use App\Domain\Exception\InvalidOperationCurrencyException;
use App\Domain\Exception\NotAllowedDailyDebitOperationCallException;
use App\Domain\Model\Operation\CreditOperation;
use App\Domain\Model\Operation\DebitOperation;
use App\Domain\Specification\AllowedDailyDebitOperationCallSpecification;
use App\Domain\VO\Money;
use App\Domain\VO\MoneyCurrency;

final class BankAccount
{
    private array $events = [];

    public function __construct(
        private Money $balance,
    ) {
    }

    public function getAmount(): float
    {
        return $this->balance->getAmount();
    }

    public function getCurrency(): MoneyCurrency
    {
        return $this->balance->getCurrency();
    }

    public function credit(CreditOperation $creditOperation): void
    {
        $this->checkOperationMoney($creditOperation->getMoney());
        $balanceAmount = $this->balance->getAmount() + $creditOperation->getAmount();
        $this->balance = new Money($balanceAmount, $this->balance->getCurrency());
    }

    public function debit(
        DebitOperation $debitOperation,
        AllowedDailyDebitOperationCallSpecification $allowedDailyDebitOperationCallSpecification,
    ): void {
        $this->checkOperationMoney($debitOperation->getMoney());

        if (!$allowedDailyDebitOperationCallSpecification->isAllowed($debitOperation->getDate())) {
            throw new NotAllowedDailyDebitOperationCallException();
        }

        if ($this->getAmount() < $debitOperation->getMoney()->getAmount()) {
            throw new InsufficientFundsException();
        }

        $balanceAmount = $this->balance->getAmount() - $debitOperation->getMoney()->getAmount();
        $this->balance = new Money($balanceAmount, $this->balance->getCurrency());
        $this->recordEvent(new DebitOperationWasCalledEvent($debitOperation->getDate()));
    }

    public function getRecordedEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }


    private function checkOperationMoney(Money $operationMoney): void
    {
        if (!$this->balance->hasSameCurrency($operationMoney)) {
            throw new InvalidOperationCurrencyException('Invalid operation currency');
        }
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
