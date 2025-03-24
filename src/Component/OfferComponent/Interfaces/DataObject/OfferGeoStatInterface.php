<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Interfaces\DataObject;

/**
 * Статистика офферов по коду.
 */
interface OfferGeoStatInterface
{
    /**
     * Вернет geo код.
     *
     * @return string
     */
    public function getGeoCode(): string;

    /**
     * Вернет количество офферов, относящихся к geo коду.
     *
     * @return int
     */
    public function getOfferCount(): int;
}
