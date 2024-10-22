<?php

declare(strict_types=1);

namespace App\Domain\VO;

final class MoneyCurrency
{
    public function __construct(
        private readonly string $code,
    ) {
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isEqual(MoneyCurrency $otherCurrency): bool
    {
        return $this->code === $otherCurrency->getCode();
    }
}
