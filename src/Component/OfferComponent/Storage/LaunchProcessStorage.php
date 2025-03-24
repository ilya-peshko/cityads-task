<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Storage;

use App\Entity\LaunchProcess;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Хранилище запусков синхронизации.
 */
readonly class LaunchProcessStorage
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Сохранит запуск в хранилище.
     *
     * @param LaunchProcess $launchProcess Запуск.
     * @param bool          $flush         Признак применения изменений.
     *
     * @return void
     */
    public function save(
        LaunchProcess $launchProcess,
        bool $flush = false,
    ): void {
        $this->entityManager->persist($launchProcess);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
