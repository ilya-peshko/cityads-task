<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\DataObject;

use App\Component\OfferComponent\Interfaces\DataObject\OfferCollectionInterface;
use App\Entity\Offer;

/**
 * Коллекция офферов.
 */
readonly class OfferCollection implements OfferCollectionInterface
{
    public function __construct(
        private array $offers,
        private int $totalCount,
    ) {
    }

    /**
     * Вернет массив офферов.
     *
     * @return Offer[]
     */
    public function getOffers(): array
    {
        return $this->offers;
    }

    /**
     * Вернет общее количество офферов.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
