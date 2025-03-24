<?php

declare(strict_types=1);

namespace App\Exception;

use App\Component\OfferComponent\Exception\OffersNotFoundException;
use App\Component\OfferComponent\Exception\OffersSyncConflictException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        match (true) {
            $exception instanceof UnprocessableEntityHttpException
            => $this->handleUnprocessableEntityHttpException($event, $exception),
            $exception instanceof NotFoundHttpException
            => $this->handleHttpNotFoundException($event, $exception),
            $exception instanceof OffersNotFoundException
            => $this->handleOffersNotFoundException($event, $exception),
            $exception instanceof OffersSyncConflictException
            => $this->handleOffersSyncConflictException($event, $exception),
            default => $this->handleUnclassifiedException($event, $exception),
        };
    }

    private function handleOffersNotFoundException(
        ExceptionEvent $event,
        OffersNotFoundException $exception,
    ): void {
        $response = $this->getErrorResponse(
            message: 'Ресурс не найден.',
            errorInfo: $exception->getMessage(),
            statusCode: Response::HTTP_NOT_FOUND,
        );

        $event->setResponse($response);
    }

    private function handleOffersSyncConflictException(
        ExceptionEvent $event,
        OffersSyncConflictException $exception,
    ): void {
        $response = $this->getErrorResponse(
            message: 'Конфликт ресурса.',
            errorInfo: $exception->getMessage(),
            statusCode: Response::HTTP_CONFLICT,
        );

        $event->setResponse($response);
    }

    private function handleHttpNotFoundException(
        ExceptionEvent $event,
        NotFoundHttpException $exception,
    ): void {
        $response = $this->getErrorResponse(
            message: 'Страница не найдена.',
            errorInfo: $exception->getMessage(),
            statusCode: $exception->getStatusCode(),
        );

        $event->setResponse($response);
    }

    private function handleUnprocessableEntityHttpException(
        ExceptionEvent $event,
        UnprocessableEntityHttpException $exception
    ): void {
        $response = $this->getErrorResponse(
            message: 'Получены некорректные данные. Проверьте параметры запроса.',
            errorInfo: $exception->getMessage(),
            statusCode: $exception->getStatusCode(),
        );

        $event->setResponse($response);
    }

    private function handleUnclassifiedException(
        ExceptionEvent $event,
        \Throwable $exception,
    ): void {
        $this->logger->error(
            message: $exception->getMessage(),
            context: ['exception' => $exception],
        );

        $response = $this->getErrorResponse(
            message: 'Ошибка сервера.',
            errorInfo: 'Произошла непредвиденная ошибка.',
        );

        $event->setResponse($response);
    }

    private function getErrorResponse(
        string $message,
        string $errorInfo = '',
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
    ): JsonResponse {
        return new JsonResponse(
            data: [
                'error' => [
                    'title' => $message,
                    'detail' => $errorInfo,
                ],
            ],
            status: $statusCode,
        );
    }
}
