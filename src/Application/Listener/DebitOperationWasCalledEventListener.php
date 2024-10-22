<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Event\DebitOperationWasCalledEvent;
use App\Domain\Repository\DailyDebitOperationCallRepository;

final class DebitOperationWasCalledEventListener
{
    public function __construct(
        private readonly DailyDebitOperationCallRepository $callRepository,
    ) {
    }

    public function apply(DebitOperationWasCalledEvent $event): void
    {
        $this->callRepository->add($event->getOperationDate());
    }
}
