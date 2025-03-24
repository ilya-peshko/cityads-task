<?php

declare(strict_types=1);

namespace App\Controller\V1;

use App\Component\OfferComponent\Exception\OffersNotFoundException;
use App\Component\OfferComponent\Exception\OffersSyncConflictException;
use App\Component\OfferComponent\OfferComponentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/api/v1/offers',
    name: 'app_v1_offer_',
)]
final class OfferController extends AbstractController
{
    public function __construct(
        private readonly OfferComponentInterface $offerComponent,
    ) {
    }

    /**
     * @throws OffersNotFoundException
     */
    #[Route(
        path: '/{geoCode}',
        name: 'get_by_geo_code',
        methods: Request::METHOD_GET,
    )]
    public function fetchByGeoCode(
        string $geoCode,
        #[MapQueryParameter(options: ['min_range' => 1])] int $page = 1,
        #[MapQueryParameter(options: ['min_range' => 1])] int $perPage = 5,
    ): JsonResponse {
        $offers = $this->offerComponent->getOffersByGeoCode(
            geoCode: $geoCode,
            page: $page,
            perPage: $perPage,
        );

        return $this->json(
            data: [
                'offers' => $offers->getOffers(),
                'meta' => [
                    'total' => $offers->getTotalCount(),
                ]
            ],
            context: ['groups' => ['offer.private']],
        );
    }

    #[Route(
        path: '/stats/geo-stats',
        name: 'get_geo_stats',
        methods: Request::METHOD_GET,
    )]
    public function fetchCountPerCode(): JsonResponse {
        $stats = $this->offerComponent->getOffersCountPerGeoCode();

        return $this->json(
            data: [
                'statistics' => $stats,
            ],
        );
    }

    /**
     * @throws OffersSyncConflictException
     * @throws ExceptionInterface
     */
    #[Route(
        path: '/sync-offers',
        name: 'post_sync_offers',
        methods: Request::METHOD_POST,
    )]
    public function syncOffers(): JsonResponse {
        $process = $this->offerComponent->asyncSyncSource();

        return $this->json(
            data: [
                'process' => [
                    'id' => $process->getId(),
                    'status' => $process->getStatus(),
                ],
            ],
            status: Response::HTTP_CREATED,
        );
    }
}
