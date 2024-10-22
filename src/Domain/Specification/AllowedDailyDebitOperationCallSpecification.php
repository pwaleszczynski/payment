<?php

declare(strict_types=1);

namespace App\Domain\Specification;

use DateTimeImmutable;

interface AllowedDailyDebitOperationCallSpecification
{
    public function isAllowed(DateTimeImmutable $operationDate): bool;
}
