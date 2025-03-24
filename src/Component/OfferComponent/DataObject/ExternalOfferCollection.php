<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\DataObject;

/**
 * Коллекция офферов, получаемых из CityAds API.
 */
readonly class ExternalOfferCollection
{
    /**
     * @param ExternalOffer[] $cityAdsOffers Массив офферов.
     * @param int             $total         Общее количество офферов.
     */
    public function __construct(
        public array $cityAdsOffers,
        public int $total,
    ) {
    }
}
