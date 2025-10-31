<?php

namespace HealthCheckBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HealthCheckBundle\Check\SqlPdoChecker;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use Laminas\Diagnostics\Check\CheckInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class CheckerService
{
    /**
     * @param iterable<CheckInterface> $builtInCheckers
     */
    public function __construct(
        #[AutowireIterator(tag: 'health-check.check')] private iterable $builtInCheckers,
        private SqlCheckerRepository $sqlCheckerRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return iterable<CheckInterface>
     */
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
