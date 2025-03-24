<?php

declare(strict_types=1);

namespace App\Component\OfferComponent;

use App\Component\OfferComponent\Exception\OffersNotFoundException;
use App\Component\OfferComponent\Exception\OffersSyncConflictException;
use App\Component\OfferComponent\Interfaces\DataObject\OfferCollectionInterface;
use App\Component\OfferComponent\Interfaces\DataObject\OfferGeoStatInterface;
use App\Component\OfferComponent\Service\AsyncLauncherService;
use App\Component\OfferComponent\Service\OfferSyncService;
use App\Entity\LaunchProcess;
use App\Repository\GeoRepository;
use App\Repository\OfferRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

/**
 * Компонент по работе с офферами.
 */
readonly class OfferComponent implements OfferComponentInterface
{
    public function __construct(
        private OfferSyncService $offerParserService,
        private OfferRepository $offerRepository,
        private GeoRepository $geoRepository,
        private AsyncLauncherService $asyncLauncherService,
    ) {
    }

    /**
     * Синхронизация данных источника.
     *
     * @return void
     */
    public function syncSource(): void
    {
        $this->offerParserService->syncOffers();
    }

    /**
     * Вернет коллекцию офферов по коду города.
     *
     * @param string $geoCode Код Geo.
     * @param int    $page    Номер страницы.
     * @param int    $perPage Количество элементов на странице.
     *
     * @return OfferCollectionInterface
     *
     * @throws OffersNotFoundException
     * @throws \Exception
     */
    public function getOffersByGeoCode(
        string $geoCode,
        int $page = 1,
        int $perPage = 5,
    ): OfferCollectionInterface {
        $geo = $this->geoRepository->findOneBy(criteria: ['code' => $geoCode]);
        if (null === $geo) {
            throw new OffersNotFoundException(
                message: 'Не удалось найти офферы по указанному geo коду.',
            );
        }

        $limit = min($perPage, 20);
        return $this->offerRepository->findByGeoCode(
            geoCode: $geoCode,
            limit: $limit,
            offset: ($page - 1) * $limit,
        );
    }

    /**
     * Вернет массив статистики офферов по коду.
     *
     * @return OfferGeoStatInterface[]
     */
    public function getOffersCountPerGeoCode(): array
    {
        return $this->offerRepository->getOffersCountPerGeoCode();
    }

    /**
     * Выполнит запуск асинхронной синхронизации данных с источником.
     *
     * @return LaunchProcess
     *
     * @throws OffersSyncConflictException
     * @throws ExceptionInterface
     */
    public function asyncSyncSource(): LaunchProcess
    {
        return $this->asyncLauncherService->startSynchronizationProcess();
    }
}
