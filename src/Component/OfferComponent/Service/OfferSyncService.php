<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Service;

use App\Component\OfferComponent\DataObject\ExternalOffer;
use App\Component\OfferComponent\Interfaces\Client\ExternalOfferApiClientInterface;
use App\Component\OfferComponent\Policy\ExcludeOfferPolicy;
use App\Component\OfferComponent\Storage\OfferStorage;
use App\Component\OfferComponent\Utils\RatingCalculator;
use App\Entity\Geo;
use App\Entity\Offer;
use App\Repository\GeoRepository;
use App\Repository\OfferRepository;
use Psr\Log\LoggerInterface;

/**
 * Сервис синхронизации офферов с источником.
 */
readonly class OfferSyncService
{
    public function __construct(
        private ExternalOfferApiClientInterface $cityAdsApiClient,
        private OfferRepository $offerRepository,
        private OfferStorage $offerStorage,
        private GeoRepository $geoRepository,
        private LoggerInterface $logger,
        private RatingCalculator $ratingCalculator,
        private ExcludeOfferPolicy $excludeOfferPolicy,
        private int $apiPerPage,
        private int $retryMaxAttempts,
    ) {
    }

    /**
     * Выполнит синхронизацию офферов.
     *
     * @return void
     */
    public function syncOffers(): void
    {
        $offersToSync = $this->getSourceOffers();
        $offersToSync = $this->extractNewSourceOffers($offersToSync);
        $offersToSync = $this->excludeOfferPolicy->apply($offersToSync);

        if (!\count($offersToSync)) {
            $this->logger->info('Новых офферов для синхронизации не найдено.');

            return;
        }

        $offers = $this->createOffers($offersToSync);

        $this->offerStorage->batchSave($offers);
    }

    /**
     * Получение офферов из API источника.
     *
     * @return ExternalOffer[]
     */
    private function getSourceOffers(): array
    {
        $offersToSync = [];
        $offersTotal = 0;

        $iteration = 1;
        $attempt = 0;
        do {
            try {
                $cityAdsOffers = $this->cityAdsApiClient->getOffers(
                    page: $iteration,
                    perPage: $this->apiPerPage,
                );

                foreach ($cityAdsOffers->cityAdsOffers as $cityAdsOffer) {
                    $offersToSync[] = $cityAdsOffer;
                }

                $offersTotal = $cityAdsOffers->total;
                $attempt = 0;
            } catch (\Throwable $exception) {
                $attempt++;
                $this->logger->warning(
                    message: \sprintf(
                        'Во время получения офферов страницы %s произошла ошибка: %s. Попытка №: %s.',
                        $iteration,
                        $exception->getMessage(),
                        $attempt,
                    ),
                    context: [
                        'exception' => $exception,
                        'perPage' => $this->apiPerPage,
                        'page' => $iteration,
                    ],
                );

                if ($attempt >= $this->retryMaxAttempts) {
                    $this->logger->error(
                        message: 'Не удалось получить все офферы. Превышен предел попыток.',
                    );

                    break;
                }
            }

            $iteration++;
        } while ((($iteration - 1) * $this->apiPerPage) < $offersTotal);

        return $offersToSync;
    }

    /**
     * Извлечет новые офферы.
     *
     * @param ExternalOffer[] $offers Массив офферов, получаемых из источника.
     *
     * @return ExternalOffer[]
     */
    private function extractNewSourceOffers(array $offers): array
    {
        $chunkSize = 250;
        $offerNames = array_column($offers, 'name');
        $notExistedNames = [];

        $offerNameChunks = array_chunk($offerNames, $chunkSize);
        foreach ($offerNameChunks as $offerNameChunk) {
            $existedNames = $this->offerRepository->findExistedNames(
                names: $offerNames,
            );

            $notExistedNames = array_merge(
                $notExistedNames,
                array_diff($offerNameChunk, $existedNames),
            );
        }

        // Исключим офферы, которые уже есть в базе.
        foreach ($offers as $key => $offer) {
            if (!\in_array($offer->name, $notExistedNames)) {
                unset($offers[$key]);
            }
        }

        return $offers;
    }

    /**
     * Создаст массив сущностей офферов.
     *
     * @param ExternalOffer[] $offers Массив офферов, получаемых из источника.
     *
     * @return Offer[]
     */
    private function createOffers(array $offers): array
    {
        $geos = [];

        // Для удобства взаимодействия преобразовываем массив сущностей geo к ассоциативному массиву по code.
        foreach ($this->geoRepository->findAll() as $geo) {
            $geos[$geo->getCode()] = $geo;
        }

        $offerEntities = [];
        foreach ($offers as $offer) {
            $offerEntity = new Offer();
            $offerEntity->setName($offer->name)
                ->setApprovalTime((int)$offer->approvalTime)
                ->setLogo($offer->logo)
                ->setOfferCurrencyName($offer->offerCurrencyName)
                ->setSiteUrl($offer->siteUrl)
                ->setRating($this->ratingCalculator->calculate($offer));

            // Добавляем к offer имеющиеся geo или создаем новое.
            foreach ($offer->geo as $geoInfo) {
                if (isset($geos[$geoInfo->code])) {
                    $offerEntity->addGeo($geos[$geoInfo->code]);
                } else {
                    $geoEntity = new Geo();
                    $geoEntity->setName($geoInfo->name)
                        ->setCode($geoInfo->code);

                    $offerEntity->addGeo($geoEntity);
                    $geos[$geoEntity->getCode()] = $geoEntity;
                }
            }

            $offerEntities[] = $offerEntity;
        }

        return $offerEntities;
    }
}
