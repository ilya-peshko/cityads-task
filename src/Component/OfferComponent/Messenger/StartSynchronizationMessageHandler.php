<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Messenger;

use App\Component\OfferComponent\OfferComponentInterface;
use App\Component\OfferComponent\Storage\LaunchProcessStorage;
use App\Enum\ProcessStatusEnum;
use App\Repository\LaunchProcessRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Обработчик сообщения запуска процесса синхронизации офферов.
 */
#[AsMessageHandler]
readonly class StartSynchronizationMessageHandler
{
    public function __construct(
        private LaunchProcessRepository $launchProcessRepository,
        private LaunchProcessStorage $launchProcessStorage,
        private OfferComponentInterface $offerComponent,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(StartSynchronizationMessage $message): void
    {
        // Проверка существования процесса.
        $process = $this->launchProcessRepository->find(id: $message->processId);
        if (null === $process) {
            throw new \InvalidArgumentException(
                message: 'Не найдено процесса с таким Id: ' . $message->processId,
            );
        }

        $this->logger->info(
            message: 'Запуск процесса синхронизации офферов. Идентификатор процесса: ' . $process->getId(),
        );

        try {
            $this->launchProcessStorage->save(
                launchProcess: $process->setStatus(ProcessStatusEnum::IN_PROGRESS),
                flush: true,
            );

            $this->offerComponent->syncSource();

            $this->logger->info('Процесс синхронизации офферов успешно завершен.');
            $process->setStatus(ProcessStatusEnum::COMPLETED);
        } catch (\Throwable $exception) {
            $this->logger->error(
                message: 'Процесс синхронизации офферов завершился с ошибкой: ' . $exception->getMessage(),
                context: ['exception' => $exception],
            );

            $process->setStatus(ProcessStatusEnum::FAILED);
        }

        $this->launchProcessStorage->save(
            launchProcess: $process,
            flush: true,
        );
    }
}
