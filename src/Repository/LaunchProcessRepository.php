<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LaunchProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LaunchProcess>
 *
 * @method LaunchProcess|null find($id, $lockMode = null, $lockVersion = null)
 * @method LaunchProcess|null findOneBy(array $criteria, array $orderBy = null)
 * @method LaunchProcess[]    findAll()
 * @method LaunchProcess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LaunchProcessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LaunchProcess::class);
    }
}
