<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Policy;

use App\Component\OfferComponent\DataObject\ExternalOffer;
use App\Component\OfferComponent\Enum\ExcludedGeoCodesEnum;

/**
 * Служба, проверяющая доступность оффера к синхронизации.
 */
class ExcludeOfferPolicy
{
    /**
     * Применяет политики выборки к офферам.
     *
     * @param ExternalOffer[] $offers Массив офферов.
     *
     * @return ExternalOffer[]
     */
    public function apply(array $offers): array
    {
        return $this->applyGeoPolicy($offers);
    }

    /**
     * Применит политику исключений по Geo для офферов.
     *
     * @param ExternalOffer[] $offers Массив офферов.
     *
     * @return ExternalOffer[]
     */
    private function applyGeoPolicy(array $offers): array
    {
        $approvedOffers = [];
        $excludedGeo = array_column(ExcludedGeoCodesEnum::cases(), 'value');
        foreach ($offers as $offer) {
            $geoCodes = array_column($offer->geo, 'code');
            if (empty(array_intersect($geoCodes, $excludedGeo))) {
                $approvedOffers[] = $offer;
            }
        }

        return $approvedOffers;
    }
}
