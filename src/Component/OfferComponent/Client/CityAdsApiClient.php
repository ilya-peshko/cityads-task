<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Client;

use App\Component\OfferComponent\DataObject\ExternalOfferCollection;
use App\Component\OfferComponent\Enum\CityAdsMethodEnum;
use App\Component\OfferComponent\Interfaces\Client\ExternalOfferApiClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * API клиент для получения офферов из источника.
 */
readonly class CityAdsApiClient implements ExternalOfferApiClientInterface
{
    private Client $client;

    public function __construct(
        private string $baseUri,
        private DenormalizerInterface $denormalizer,
    ) {
        $this->client = new Client(
            config: [
                'base_uri' => $this->baseUri,
                'timeout' => 60,
                'port' => 443,
            ],
        );
    }

    /**
     * Вернет коллекцию офферов.
     *
     * @param int $page    Номер страницы.
     * @param int $perPage Количество элементов на странице.
     *
     * @return ExternalOfferCollection
     *
     * @throws GuzzleException
     * @throws ExceptionInterface
     */
    public function getOffers(
        int $page = 1,
        int $perPage = 20,
    ): ExternalOfferCollection {
        $response = $this->client->get(
            uri: CityAdsMethodEnum::V2_OFFERS_LIST->value,
            options: [
                RequestOptions::QUERY => [
                    'page' => $page,
                    'perpage' => $perPage,
                ],
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                ],
            ],
        );

        $data = \json_decode(
            json: $response->getBody()->getContents(),
            associative: true,
        );

        return $this->denormalizer->denormalize(
            data: $data,
            type: ExternalOfferCollection::class,
        );
    }
}
