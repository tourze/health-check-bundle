<?php

namespace HealthCheckBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HealthCheckBundle\Check\SqlPdoChecker;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class CheckerService
{
    public function __construct(
        #[TaggedIterator('health-check.check')] private readonly iterable $builtInCheckers,
        private readonly SqlCheckerRepository $sqlCheckerRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getCheckers(): iterable
    {
        foreach ($this->builtInCheckers as $checker) {
            yield $checker;
        }

        // 查找数据库中的配置
        foreach ($this->sqlCheckerRepository->findEnabled() as $item) {
            yield new SqlPdoChecker($item, $this->entityManager->getConnection());
        }
    }
}
