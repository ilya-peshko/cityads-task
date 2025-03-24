<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Geo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Geo>
 *
 * @method Geo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Geo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Geo[]    findAll()
 * @method Geo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Geo::class);
    }
}
