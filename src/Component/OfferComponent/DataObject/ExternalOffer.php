<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\DataObject;

/**
 * Оффер, получаемый из источника.
 */
readonly class ExternalOffer
{
    /**
     * @param string                 $id                ID оффера.
     * @param string                 $name              Название оффера.
     * @param string                 $offerCurrencyName Название валюты.
     * @param string                 $logo              Логотип.
     * @param string                 $approvalTime      Время подтверждения.
     * @param string                 $paymentTime       Время оплаты.
     * @param string                 $statEcpl          Рассчетная метрика ecpl.
     * @param string                 $siteUrl           URL сайта.
     * @param ExternalOfferGeoInfo[] $geo               Geo информация оффера.
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $offerCurrencyName,
        public string $logo,
        public string $approvalTime,
        public string $paymentTime,
        public string $statEcpl,
        public string $siteUrl,
        public array $geo,
    ) {
    }
}
