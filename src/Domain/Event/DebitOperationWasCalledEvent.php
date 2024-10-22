<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

final class DebitOperationWasCalledEvent implements DomainEvent
{
    public function __construct(
        private readonly DateTimeImmutable $operationDate,
    ) {
    }

    public function getOperationDate(): DateTimeImmutable
    {
        return $this->operationDate;
    }
}
