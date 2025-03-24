<?php

declare(strict_types=1);

namespace App\Component\OfferComponent\Service;

use App\Component\OfferComponent\Exception\OffersSyncConflictException;
use App\Component\OfferComponent\Messenger\StartSynchronizationMessage;
use App\Component\OfferComponent\Storage\LaunchProcessStorage;
use App\Entity\LaunchProcess;
use App\Enum\ProcessStatusEnum;
use App\Repository\LaunchProcessRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Сервис запуска асинхронного процесса синхронизации офферов.
 */
readonly class AsyncLauncherService
{
    public function __construct(
        private LaunchProcessRepository $launchProcessRepository,
        private LaunchProcessStorage $launchProcessStorage,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Запустит процесс асинхронной синхронизации офферов.
     *
     * @return LaunchProcess
     *
     * @throws OffersSyncConflictException
     * @throws ExceptionInterface
     */
    public function startSynchronizationProcess(): LaunchProcess
    {
        $process = $this->launchProcessRepository->findOneBy(
            criteria: [
                'status' => [
                    ProcessStatusEnum::PENDING,
                    ProcessStatusEnum::IN_PROGRESS,
                ],
            ],
        );

        if (null !== $process) {
            $this->logger->error(
                message: 'Попытка повторного запуска синхронизации. '
                .'Идентификатор активного процесса: ' . $process->getId(),
            );

            throw new OffersSyncConflictException(
                message: 'Процесс синхронизации уже запущен. Идентификатор процесса: ' . $process->getId(),
            );
        }

        $process = new LaunchProcess();
        $process->setStatus(ProcessStatusEnum::PENDING);
        $this->launchProcessStorage->save(launchProcess: $process, flush: true);

        $this->messageBus->dispatch(new StartSynchronizationMessage(
            processId: $process->getId(),
        ));

        return $process;
    }
}
