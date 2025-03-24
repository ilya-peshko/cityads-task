<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Exception;

use Throwable;

class OffersNotFoundException extends \Exception
{
    public function __construct(
        string $message = "Не удалось найти офферы.",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
