<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Enum;

/**
 * Перечисления методов CityAds API.
 */
enum CityAdsMethodEnum: string
{
    /**
     * Метод получения списка офферов по разным параметрам.
     */
    case V2_OFFERS_LIST = 'api/rest/webmaster/v2/offers/list';
}
