<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Messenger;

use Symfony\Component\Messenger\Attribute\AsMessage;

/**
 * Сообщение для запуска процесса синхронизации офферов.
 */
#[AsMessage]
readonly class StartSynchronizationMessage
{
    public function __construct(
        public int $processId,
    ) {
    }
}
