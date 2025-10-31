<?php

namespace HealthCheckBundle\Tests\Repository;

use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use HealthCheckBundle\Repository\SqlCheckerRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SqlCheckerRepository::class)]
#[RunTestsInSeparateProcesses]
final class SqlCheckerRepositoryTest extends AbstractRepositoryTestCase
{
    private SqlCheckerRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SqlCheckerRepository::class);
    }

    public function testRepositoryInheritance(): void
    {
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $this->repository);
    }

    public function testFindEnabled(): void
    {
        // 清理数据库
        $this->cleanupDatabase();

        // 创建启用的检查器
        $enabledChecker = $this->createSqlChecker('启用检查', true);
        $this->repository->save($enabledChecker);

        // 创建禁用的检查器
        $disabledChecker = $this->createSqlChecker('禁用检查', false);
        $this->repository->save($disabledChecker);

        $result = $this->repository->findEnabled();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('启用检查', $result[0]->getName());
        $this->assertTrue($result[0]->isValid());
    }

    public function testSave(): void
    {
        $this->cleanupDatabase();

        $sqlChecker = $this->createSqlChecker('保存测试');
        $this->repository->save($sqlChecker);

        $this->assertNotNull($sqlChecker->getId());
        $this->assertGreaterThan(0, $sqlChecker->getId());

        // 验证数据库中确实保存了数据
        $found = $this->repository->find($sqlChecker->getId());
        $this->assertInstanceOf(SqlChecker::class, $found);
        $this->assertEquals('保存测试', $found->getName());
    }

    public function testSaveWithoutFlush(): void
    {
        $this->cleanupDatabase();

        $sqlChecker = $this->createSqlChecker('不刷新保存');
        $this->repository->save($sqlChecker, false);

        // 手动刷新
        self::getEntityManager()->flush();

        $found = $this->repository->find($sqlChecker->getId());
        $this->assertInstanceOf(SqlChecker::class, $found);
        $this->assertEquals('不刷新保存', $found->getName());
    }

    public function testRemove(): void
    {
        $this->cleanupDatabase();

        $sqlChecker = $this->createSqlChecker('删除测试');
        $this->repository->save($sqlChecker);
        $id = $sqlChecker->getId();

        // 确认存在
        $this->assertNotNull($this->repository->find($id));

        // 删除
        $this->repository->remove($sqlChecker);

        // 确认已删除
        $this->assertNull($this->repository->find($id));
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $this->cleanupDatabase();

        // 创建多个检查器，按创建时间不同
        $checker1 = $this->createSqlChecker('第一个检查器');
        $this->repository->save($checker1);

        $checker2 = $this->createSqlChecker('第二个检查器');
        $this->repository->save($checker2);

        // 测试按名称排序
        $result = $this->repository->findOneBy([], ['name' => 'ASC']);
        $this->assertInstanceOf(SqlChecker::class, $result);
        $this->assertEquals('第一个检查器', $result->getName());

        $result = $this->repository->findOneBy([], ['name' => 'DESC']);
        $this->assertInstanceOf(SqlChecker::class, $result);
        $this->assertEquals('第二个检查器', $result->getName());
    }

    private function createSqlChecker(string $name, ?bool $valid = true): SqlChecker
    {
        $sqlChecker = new SqlChecker();
        $sqlChecker->setName($name);
        $sqlChecker->setSql('SELECT COUNT(*) FROM test_table');
        $sqlChecker->setCronExpression('* * * * *');
        $sqlChecker->setOperator(SqlOperatorEnum::EQ);
        $sqlChecker->setCompareValue(1);
        $sqlChecker->setValid($valid);

        return $sqlChecker;
    }

    public function testFindByWithNullValueQueries(): void
    {
        $this->cleanupDatabase();

        // 创建一个备注为空的检查器
        $checkerWithNullRemark = $this->createSqlChecker('空备注检查');
        $checkerWithNullRemark->setRemark(null);
        $this->repository->save($checkerWithNullRemark);

        // 创建一个有备注的检查器
        $checkerWithRemark = $this->createSqlChecker('有备注检查');
        $checkerWithRemark->setRemark('这是备注');
        $this->repository->save($checkerWithRemark);

        // 测试 IS NULL 查询
        $nullRemarkResult = $this->repository->findBy(['remark' => null]);
        $this->assertCount(1, $nullRemarkResult);
        $this->assertEquals('空备注检查', $nullRemarkResult[0]->getName());

        // 测试 IS NOT NULL 查询（通过查找非null值）
        $nonNullRemarkResult = $this->repository->findBy(['remark' => '这是备注']);
        $this->assertCount(1, $nonNullRemarkResult);
        $this->assertEquals('有备注检查', $nonNullRemarkResult[0]->getName());
    }

    public function testCountWithNullValueQueries(): void
    {
        $this->cleanupDatabase();

        // 创建一个valid为null的检查器
        $checkerWithNullValid = $this->createSqlChecker('空有效性检查');
        $checkerWithNullValid->setValid(null);
        $this->repository->save($checkerWithNullValid);

        // 创建一个valid为true的检查器
        $checkerWithValid = $this->createSqlChecker('有效检查', true);
        $this->repository->save($checkerWithValid);

        // 测试 count IS NULL 查询
        $nullValidCount = $this->repository->count(['valid' => null]);
        $this->assertEquals(1, $nullValidCount);

        // 测试 count IS NOT NULL 查询
        $trueValidCount = $this->repository->count(['valid' => true]);
        $this->assertEquals(1, $trueValidCount);
    }

    public function testFindByWithInvalidFieldShouldHandleException(): void
    {
        $this->expectException(UnrecognizedField::class);
        $this->repository->findBy(['invalidFieldName' => 'value']);
    }

    private function cleanupDatabase(): void
    {
        // 清理所有 SqlChecker 数据
        self::getEntityManager()->createQuery('DELETE FROM HealthCheckBundle\Entity\SqlChecker')->execute();
    }

    protected function getRepository(): SqlCheckerRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $name = 'Test SqlChecker - ' . uniqid();
        $sql = 'SELECT COUNT(*) FROM test_table_' . uniqid();
        $sqlChecker = new SqlChecker();
        $sqlChecker->setName($name);
        $sqlChecker->setSql($sql);
        $sqlChecker->setCronExpression('0 * * * *');
        $sqlChecker->setOperator(SqlOperatorEnum::EQ);
        $sqlChecker->setCompareValue(1);
        $sqlChecker->setValid(true);

        return $sqlChecker;
    }
}
