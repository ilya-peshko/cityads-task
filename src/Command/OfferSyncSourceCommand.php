<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\OfferComponent\OfferComponentInterface;
use App\Component\OfferComponent\Storage\LaunchProcessStorage;
use App\Entity\LaunchProcess;
use App\Enum\ProcessStatusEnum;
use App\Repository\LaunchProcessRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:offer:sync-source',
    description: 'Команда для синхронизации данных с источника.',
)]
class OfferSyncSourceCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly OfferComponentInterface $offerComponent,
        private readonly LaunchProcessRepository $launchProcessRepository,
        private readonly LaunchProcessStorage $launchProcessStorage,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        // Проверка наличия активного процесса синхронизации.
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

            $io->error(message: 'Уже есть активный процесс синхронизации.');

            return Command::FAILURE;
        }

        // Создание нового процесса запуска синхронизации.
        $process = new LaunchProcess();
        $process->setStatus(status: ProcessStatusEnum::IN_PROGRESS);
        $this->launchProcessStorage->save(launchProcess: $process, flush: true);

        try {
            $io->info(message: 'Сбор данных с источника...');

            $this->offerComponent->syncSource();

            $process->setStatus(status: ProcessStatusEnum::COMPLETED);
        } catch (\Throwable $exception) {
            $this->logger->error(
                message: 'При сборе данных с источника возникла ошибка: ' . $exception->getMessage(),
                context: ['exception' => $exception],
            );

            $io->error(message: 'Произошла ошибка при сборе данных с источника.');
            $io->error(message: $exception->getMessage());

            $process->setStatus(status: ProcessStatusEnum::FAILED);

            return Command::FAILURE;
        }
        $io->success(message: 'Данные успешно обработаны.');

        $this->launchProcessStorage->save(launchProcess: $process, flush: true);

        return Command::SUCCESS;
    }
}
