<?php

namespace HealthCheckBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HealthCheckBundle\Entity\SqlChecker;

/**
 * @method SqlChecker|null find($id, $lockMode = null, $lockVersion = null)
 * @method SqlChecker|null findOneBy(array $criteria, array $orderBy = null)
 * @method SqlChecker[] findAll()
 * @method SqlChecker[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SqlCheckerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SqlChecker::class);
    }

    /**
     * @return SqlChecker[]
     */
    public function findEnabled(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.valid = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }
}
