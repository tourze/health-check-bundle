<?php

namespace HealthCheckBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HealthCheckBundle\Entity\SqlChecker;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<SqlChecker>
 */
#[AsRepository(entityClass: SqlChecker::class)]
class SqlCheckerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SqlChecker::class);
    }

    /**
     * @return array<SqlChecker>
     */
    public function findEnabled(): array
    {
        /** @var array<SqlChecker> */
        return $this->createQueryBuilder('s')
            ->andWhere('s.valid = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(SqlChecker $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SqlChecker $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
