<?php

declare(strict_types=1);

namespace Tests\unit\Domain\Specification;
use App\Domain\Repository\DailyDebitOperationCallRepository;
use App\Domain\Specification\ParticularAllowedDailyDebitOperationCallSpecification;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ParticularAllowedDailyDebitOperationCallSpecificationTest extends TestCase
{
    public function testShouldAllow(): void
    {
        $repositoryMock = $this->createMock(DailyDebitOperationCallRepository::class);
        $repositoryMock->method('getCount')->willReturn(1);
        $allowedCalls = 3;
        $spec = new ParticularAllowedDailyDebitOperationCallSpecification($allowedCalls, $repositoryMock);

        self::assertTrue($spec->isAllowed(new DateTimeImmutable()));
    }

    public function testNotShouldAllow(): void
    {
        $repositoryMock = $this->createMock(DailyDebitOperationCallRepository::class);
        $repositoryMock->method('getCount')->willReturn(3);
        $allowedCalls = 3;
        $spec = new ParticularAllowedDailyDebitOperationCallSpecification($allowedCalls, $repositoryMock);

        self::assertFalse($spec->isAllowed(new DateTimeImmutable()));
    }
}