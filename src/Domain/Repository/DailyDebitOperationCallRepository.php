<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use DateTimeImmutable;

interface DailyDebitOperationCallRepository
{
    public function add(DateTimeImmutable $operationDate): void;
    public function getCount(DateTimeImmutable $day): int;
}
