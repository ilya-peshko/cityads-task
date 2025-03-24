<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Utils;

use App\Component\OfferComponent\DataObject\ExternalOffer;

/**
 * Служба подсчета рейтинга.
 */
class RatingCalculator
{
    /**
     * Рассчитает рейтинг на основе полученных данных источника.
     *
     * @param ExternalOffer $offer Оффер, получаемый из источника.
     *
     * @return float
     */
    public function calculate(ExternalOffer $offer): float
    {
        $firstCoefficient = 10 * (1 - $offer->approvalTime / 90);
        $secondCoefficient = 100 * (1 - $offer->paymentTime / 90);

        if (0 === $firstCoefficient || 0 === $secondCoefficient) {
            $calculatedValue = (float) $offer->statEcpl;
        } else {
            $calculatedValue = $offer->statEcpl * $firstCoefficient * $secondCoefficient;
        }

        return round($calculatedValue, 2);
    }
}
