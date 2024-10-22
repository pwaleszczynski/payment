<?php

declare(strict_types=1);

namespace App\Domain\Specification;

use App\Domain\Repository\DailyDebitOperationCallRepository;
use DateTimeImmutable;

final class ParticularAllowedDailyDebitOperationCallSpecification implements AllowedDailyDebitOperationCallSpecification
{
    public function __construct(
        private readonly int $allowedCalls,
        private readonly DailyDebitOperationCallRepository $repository,
    ){
    }

    public function isAllowed(DateTimeImmutable $operationDate): bool
    {
        return !($this->repository->getCount($operationDate) >= $this->allowedCalls);
    }
}
