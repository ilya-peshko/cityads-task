<?php

declare(strict_types=1);

namespace App\Component\OfferComponent;

use App\Component\OfferComponent\Exception\OffersNotFoundException;
use App\Component\OfferComponent\Exception\OffersSyncConflictException;
use App\Component\OfferComponent\Interfaces\DataObject\OfferCollectionInterface;
use App\Component\OfferComponent\Interfaces\DataObject\OfferGeoStatInterface;
use App\Entity\LaunchProcess;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

/**
 * Компонент по работе с офферами.
 */
interface OfferComponentInterface
{
    /**
     * Синхронизация данных источника.
     *
     * @return void
     */
    public function syncSource(): void;

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
     */
    public function getOffersByGeoCode(
        string $geoCode,
        int $page = 1,
        int $perPage = 5,
    ): OfferCollectionInterface;

    /**
     * Вернет массив статистики офферов по коду.
     *
     * @return OfferGeoStatInterface[]
     */
    public function getOffersCountPerGeoCode(): array;

    /**
     * Выполнит запуск асинхронной синхронизации данных с источником.
     *
     * @return LaunchProcess
     *
     * @throws OffersSyncConflictException
     * @throws ExceptionInterface
     */
    public function asyncSyncSource(): LaunchProcess;
}
