<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Denormalizer;

use App\Component\OfferComponent\DataObject\ExternalOfferGeoInfo;
use App\Component\OfferComponent\DataObject\ExternalOffer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Преобразователь объекта CityAdsOffer.
 */
class CityAdsOfferDenormalizer implements DenormalizerInterface
{
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): mixed {
        $geoInfo = [];
        foreach ($data['geo'] as $geo) {
            if (empty($geo['code']) || empty($geo['name'])) {
                continue;
            }

            $geoInfo[] = new ExternalOfferGeoInfo(
                code: $geo['code'],
                name: $geo['name'],
            );
        }

       return new ExternalOffer(
           id: $data['id'],
           name: $data['name'],
           offerCurrencyName: $data['offer_currency']['name'],
           logo: $data['logo'],
           approvalTime: $data['approval_time'],
           paymentTime: $data['payment_time'],
           statEcpl: $data['stat']['ecpl'],
           siteUrl: $data['site_url'],
           geo: $geoInfo,
       );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return ExternalOffer::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ExternalOffer::class => true,
        ];
    }
}
