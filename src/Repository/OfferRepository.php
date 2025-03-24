<?php

declare(strict_types=1);

namespace App\Repository;

use App\Component\OfferComponent\DataObject\OfferCollection;
use App\Component\OfferComponent\DataObject\OfferGeoStat;
use App\Component\OfferComponent\Interfaces\DataObject\OfferCollectionInterface;
use App\Component\OfferComponent\Interfaces\DataObject\OfferGeoStatInterface;
use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 *
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * Вернет массив существующих имен.
     *
     * @param array $names Массив имен для поиска.
     *
     * @return array
     */
    public function findExistedNames(array $names): array
    {
        $qb =  $this->createQueryBuilder('offer');
        $result = $qb->select('offer.name')
            ->where($qb->expr()->in('offer.name', ':names'))
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult();

        return array_column($result, 'name');
    }

    /**
     * Вернет коллекцию офферов по коду города.
     *
     * @param string $geoCode Код Geo.
     * @param int    $limit   Количество возвращаемых элементов.
     * @param int    $offset  Смещение возвращаемых элементов.
     *
     * @return OfferCollectionInterface
     *
     * @throws \Exception
     */
    public function findByGeoCode(
        string $geoCode,
        int $limit = 5,
        int $offset = 0,
    ): OfferCollectionInterface {
        $query = $this->createQueryBuilder('o')
            ->innerJoin('o.geo', 'g')
            ->where('g.code = :geoCode')
            ->setParameter('geoCode', $geoCode)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('o.rating', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);

        return new OfferCollection(
            offers: $paginator->getIterator()->getArrayCopy(),
            totalCount: $paginator->count(),
        );
    }

    /**
     * Вернет массив количества офферов для каждого geo кода.
     *
     * @return OfferGeoStatInterface[]
     */
    public function getOffersCountPerGeoCode(): array
    {
        $query = $this->createQueryBuilder('o')
            ->select('g.code, COUNT(o.id) AS count')
            ->innerJoin('o.geo', 'g')
            ->groupBy('g.code')
            ->getQuery();

        $geoStats = [];
        foreach ($query->getResult() as $row) {
            $geoStats[] = new OfferGeoStat(
                geoCode: $row['code'],
                offerCount: $row['count'],
            );
        }

        return $geoStats;
    }
}
