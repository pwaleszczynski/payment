<?php

declare(strict_types=1);

namespace App\Domain\Model\Operation;

use App\Domain\VO\Money;
use DateTimeImmutable;

interface DebitOperation
{
    public function getMoney(): Money;
    public function getDate(): DateTimeImmutable;
}
