<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\DataObject;

use App\Component\OfferComponent\Interfaces\DataObject\OfferGeoStatInterface;

/**
 * Статистика офферов по коду.
 */
readonly class OfferGeoStat implements OfferGeoStatInterface
{
    public function __construct(
        public string $geoCode,
        public int $offerCount,
    ) {
    }

    /**
     * Вернет geo код.
     *
     * @return string
     */
    public function getGeoCode(): string
    {
        return $this->geoCode;
    }

    /**
     * Вернет количество офферов, относящихся к geo коду.
     *
     * @return int
     */
    public function getOfferCount(): int
    {
        return $this->offerCount;
    }
}
