<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Denormalizer;

use App\Component\OfferComponent\DataObject\ExternalOffer;
use App\Component\OfferComponent\DataObject\ExternalOfferCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

readonly class CityAdsOfferCollectionDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private CityAdsOfferDenormalizer $cityAdsOfferDenormalizer,
    ) {
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): mixed {
        $offers = [];
        if (!isset($data['offers']) || !isset($data['meta']['total'])) {
            throw new \InvalidArgumentException(
                'Отсутствует обязательный параметр ответа. Денормализация коллекции невозможна.',
            );
        }

        foreach ($data['offers'] as $offer) {
            $offers[] = $this->cityAdsOfferDenormalizer->denormalize(
                data: $offer,
                type: ExternalOffer::class,
            );
        }

        return new ExternalOfferCollection(
            cityAdsOffers: $offers,
            total: $data['meta']['total'],
        );
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return ExternalOfferCollection::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ExternalOfferCollection::class => true,
        ];
    }
}
