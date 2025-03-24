<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\DataObject;

/**
 * Geo информация оффера.
 */
readonly class ExternalOfferGeoInfo
{
    /**
     * @param string $code Код страны.
     * @param string $name Название страны.
     */
    public function __construct(
        public string $code,
        public string $name,
    ) {
    }
}
