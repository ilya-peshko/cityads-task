<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Storage;

use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Хранилище офферов.
 */
readonly class OfferStorage
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private int $batchSize,
    ) {
    }

    /**
     * Сохранит оффер в хранилище.
     *
     * @param Offer $offer Оффер.
     * @param bool  $flush Признак применения изменений.
     *
     * @return void
     */
    public function save(
        Offer $offer,
        bool $flush = false,
    ): void {
        $this->entityManager->persist($offer);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Выполнит сохрание офферов в хранилище с делением на партии.
     *
     * @param Offer[] $offers Массив офферов.
     *
     * @return void
     */
    public function batchSave(array $offers): void
    {
        $iteration = 0;
        foreach ($offers as $offer) {
            $withFlush = 0 === ($iteration % $this->batchSize);

            $this->save($offer, $withFlush);
            $iteration++;
        }

        $this->entityManager->flush();
    }
}
