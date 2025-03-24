<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Interfaces\DataObject;

use App\Entity\Offer;

/**
 * Коллекция офферов.
 */
interface OfferCollectionInterface
{
    /**
     * Вернет массив офферов.
     *
     * @return Offer[]
     */
    public function getOffers(): array;

    /**
     * Вернет общее количество офферов.
     *
     * @return int
     */
    public function getTotalCount(): int;
}
