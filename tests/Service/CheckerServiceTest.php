<?php

namespace HealthCheckBundle\Tests\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use HealthCheckBundle\Check\SqlPdoChecker;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use HealthCheckBundle\Service\CheckerService;
use Laminas\Diagnostics\Check\CheckInterface;
use PHPUnit\Framework\TestCase;

class CheckerServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface&MockObject
     */
    private $entityManager;

    /**
     * @var SqlCheckerRepository&MockObject
     */
    private $sqlCheckerRepository;

    /**
     * @var array<CheckInterface&MockObject>
     */
    private array $builtInCheckers;

    /**
     * @var Connection&MockObject
     */
    private $connection;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sqlCheckerRepository = $this->createMock(SqlCheckerRepository::class);
        $this->connection = $this->createMock(Connection::class);

        // 创建模拟的内置检查器
        $builtInChecker1 = $this->createMock(CheckInterface::class);
        $builtInChecker2 = $this->createMock(CheckInterface::class);

        $this->builtInCheckers = [$builtInChecker1, $builtInChecker2];

        // 设置 EntityManager 返回连接
        $this->entityManager->method('getConnection')
            ->willReturn($this->connection);
    }

    public function testGetCheckersWithNoEnabledSqlCheckers(): void
    {
        // 设置不返回任何启用的 SQL 检查
        $this->sqlCheckerRepository->method('findEnabled')
            ->willReturn([]);

        $service = new CheckerService(
            $this->builtInCheckers,
            $this->sqlCheckerRepository,
            $this->entityManager
        );

        $checkers = iterator_to_array($service->getCheckers());

        // 应该只返回内置检查器
        $this->assertCount(2, $checkers);
        $this->assertSame($this->builtInCheckers[0], $checkers[0]);
        $this->assertSame($this->builtInCheckers[1], $checkers[1]);
    }

    public function testGetCheckersWithEnabledSqlCheckers(): void
    {
        // 创建一些模拟的 SqlChecker 实体
        $sqlChecker1 = $this->createConfiguredMock(SqlChecker::class, [
            'getName' => '测试SQL检查1',
        ]);

        $sqlChecker2 = $this->createConfiguredMock(SqlChecker::class, [
            'getName' => '测试SQL检查2',
        ]);

        // 设置返回启用的 SQL 检查
        $this->sqlCheckerRepository->method('findEnabled')
            ->willReturn([$sqlChecker1, $sqlChecker2]);

        $service = new CheckerService(
            $this->builtInCheckers,
            $this->sqlCheckerRepository,
            $this->entityManager
        );

        $checkers = iterator_to_array($service->getCheckers());

        // 应该返回内置检查器加上从数据库中找到的检查器
        $this->assertCount(4, $checkers);

        // 前两个是内置检查器
        $this->assertSame($this->builtInCheckers[0], $checkers[0]);
        $this->assertSame($this->builtInCheckers[1], $checkers[1]);

        // 后两个是从数据库生成的 SqlPdoChecker
        $this->assertInstanceOf(SqlPdoChecker::class, $checkers[2]);
        $this->assertInstanceOf(SqlPdoChecker::class, $checkers[3]);
    }
}
