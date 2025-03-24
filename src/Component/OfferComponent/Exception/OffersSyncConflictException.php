<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Exception;

use Throwable;

class OffersSyncConflictException extends \Exception
{
    public function __construct(
        string $message = "Процесс синхронизации уже запущен.",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
