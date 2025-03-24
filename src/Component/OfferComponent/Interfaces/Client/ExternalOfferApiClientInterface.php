<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Interfaces\Client;

use App\Component\OfferComponent\DataObject\ExternalOfferCollection;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * API клиент для получения офферов из источника.
 */
interface ExternalOfferApiClientInterface
{
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
    ): ExternalOfferCollection;
}
