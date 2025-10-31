<?php

namespace HealthCheckBundle\Tests\Check;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Result;
use HealthCheckBundle\Check\SqlPdoChecker;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SqlPdoChecker::class)]
final class SqlPdoCheckerTest extends TestCase
{
    private TestableSqlCheckerInterface $sqlChecker;

    private TestableConnectionInterface $connection;

    private SqlPdoChecker $checker;

    private TestableResultInterface $result;

    protected function setUp(): void
    {
        parent::setUp();
        /*
         * 使用匿名类替代Mock以满足PHPStan静态分析要求
         * 匿名类实现了测试所需的方法，保持测试行为不变
         */

        // 创建SqlChecker匿名类实例
        $sqlChecker = new class extends SqlChecker implements TestableSqlCheckerInterface {
            private string $testName = '';

            private string $testCronExpression = '';

            private string $testSql = '';

            private SqlOperatorEnum $testOperator = SqlOperatorEnum::EQ;

            private int $testCompareValue = 0;

            private int $nameCallCount = 0;

            public function getName(): string
            {
                ++$this->nameCallCount;

                return $this->testName;
            }

            public function getCronExpression(): string
            {
                return $this->testCronExpression;
            }

            public function getSql(): string
            {
                return $this->testSql;
            }

            public function getOperator(): SqlOperatorEnum
            {
                return $this->testOperator;
            }

            public function getCompareValue(): int
            {
                return $this->testCompareValue;
            }

            // 测试辅助方法
            public function setTestName(string $name): void
            {
                $this->testName = $name;
            }

            public function setTestCronExpression(string $cronExpression): void
            {
                $this->testCronExpression = $cronExpression;
            }

            public function setTestSql(string $sql): void
            {
                $this->testSql = $sql;
            }

            public function setTestOperator(SqlOperatorEnum $operator): void
            {
                $this->testOperator = $operator;
            }

            public function setTestCompareValue(int $compareValue): void
            {
                $this->testCompareValue = $compareValue;
            }

            public function getNameCallCount(): int
            {
                return $this->nameCallCount;
            }
        };

        // 将sqlChecker实例赋值给类属性
        $this->sqlChecker = $sqlChecker;

        // 创建Connection匿名类实例
        $connection = new class implements TestableConnectionInterface {
            private ?TestableResultInterface $testResult = null;

            private ?\Exception $testException = null;

            /**
             * @param array<int|string, mixed> $params
             * @param array<int|string, mixed> $types
             */
            public function executeQuery(string $sql, array $params = [], array $types = [], ?QueryCacheProfile $qcp = null): Result
            {
                if (null !== $this->testException) {
                    throw $this->testException;
                }

                if (null === $this->testResult) {
                    throw new \RuntimeException('Test result not configured');
                }

                // 将TestableResultInterface包装为真实的Result
                $value = $this->testResult->fetchOne();

                // 如果测试结果为false，创建一个返回false的PDO语句
                if (false === $value) {
                    $pdo = new \PDO('sqlite::memory:');
                    $stmt = $pdo->prepare('SELECT 1 WHERE 1=0'); // 这个查询永远不会返回结果
                    $stmt->execute();
                    $driverResult = new Driver\PDO\Result($stmt);

                    return new Result($driverResult, new Connection(['driver' => 'pdo_sqlite', 'memory' => true], new Driver\PDO\SQLite\Driver()));
                }

                // 否则创建正常的结果
                $pdo = new \PDO('sqlite::memory:');
                $stmt = $pdo->prepare('SELECT ?');
                $stmt->execute([$value]);
                $driverResult = new Driver\PDO\Result($stmt);

                return new Result($driverResult, new Connection(['driver' => 'pdo_sqlite', 'memory' => true], new Driver\PDO\SQLite\Driver()));
            }

            // 测试辅助方法
            public function setTestResult(TestableResultInterface $result): void
            {
                $this->testResult = $result;
            }

            public function setTestException(\Exception $exception): void
            {
                $this->testException = $exception;
            }
        };

        // 将connection实例赋值给类属性
        $this->connection = $connection;

        // 创建Result匿名类实例
        $result = new class implements TestableResultInterface {
            private mixed $testValue = null;

            public function fetchOne(): mixed
            {
                return $this->testValue;
            }

            // 测试辅助方法
            public function setTestValue(mixed $value): void
            {
                $this->testValue = $value;
            }
        };

        // 将result实例赋值给类属性
        $this->result = $result;

        // 使用适配器将测试Connection包装为真实的Connection
        $connectionAdapter = ConnectionAdapter::createForTest($this->connection);

        // 因为SqlPdoChecker需要SqlChecker，而我们的测试对象是SqlChecker的子类，所以可以直接使用
        $this->checker = new SqlPdoChecker($this->sqlChecker, $connectionAdapter);
    }

    public function testGetLabel(): void
    {
        $this->sqlChecker->setTestName('测试SQL检查');

        $this->assertEquals('SQL检查: 测试SQL检查', $this->checker->getLabel());
    }

    public function testCheckWhenNotDue(): void
    {
        // 设置 cron 表达式为非当前时间
        $this->sqlChecker->setTestCronExpression('0 0 31 2 *'); // 2月31日，这一天不存在，所以永远不会到期

        $result = $this->checker->check();

        $this->assertInstanceOf(Skip::class, $result);
        $this->assertEquals('不在检查时间范围内', $result->getMessage());
    }

    public function testCheckWhenSqlFailsWithNoResult(): void
    {
        // 设置 cron 表达式为当前时间（每分钟）
        $this->sqlChecker->setTestCronExpression('* * * * *');
        $this->sqlChecker->setTestSql('SELECT COUNT(*) FROM users');
        $this->sqlChecker->setTestName('用户计数');

        $this->result->setTestValue(false);
        $this->connection->setTestResult($this->result);

        $result = $this->checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('[用户计数] SQL执行失败: 未返回数据', $result->getMessage());
    }

    public function testCheckWhenSqlThrowsException(): void
    {
        // 设置 cron 表达式为当前时间
        $this->sqlChecker->setTestCronExpression('* * * * *');
        $this->sqlChecker->setTestSql('INVALID SQL');
        $this->sqlChecker->setTestName('无效SQL');

        $this->connection->setTestException(new \Exception('SQL语法错误'));

        $result = $this->checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('[无效SQL] SQL执行失败: SQL语法错误', $result->getMessage());
    }

    #[DataProvider('operatorDataProvider')]
    public function testCheckWithDifferentOperators(
        SqlOperatorEnum $operator,
        int $value,
        int $compareValue,
        bool $expectedValid,
    ): void {
        // 设置 cron 表达式为当前时间
        $this->sqlChecker->setTestCronExpression('* * * * *');
        $this->sqlChecker->setTestSql('SELECT COUNT(*) FROM table');
        $this->sqlChecker->setTestOperator($operator);
        $this->sqlChecker->setTestCompareValue($compareValue);
        $this->sqlChecker->setTestName('测试检查');

        $this->result->setTestValue($value);
        $this->connection->setTestResult($this->result);

        $result = $this->checker->check();

        if ($expectedValid) {
            $this->assertInstanceOf(Success::class, $result);
            $this->assertStringContainsString('[测试检查] SQL检查通过', $result->getMessage());
        } else {
            $this->assertInstanceOf(Failure::class, $result);
            $this->assertStringContainsString('[测试检查] SQL检查未通过', $result->getMessage());
        }
    }

    /**
     * @return array<string, array{SqlOperatorEnum, int, int, bool}>
     */
    public static function operatorDataProvider(): array
    {
        return [
            'GT_true' => [SqlOperatorEnum::GT, 10, 5, true],
            'GT_false' => [SqlOperatorEnum::GT, 5, 10, false],
            'GTE_true_greater' => [SqlOperatorEnum::GTE, 10, 5, true],
            'GTE_true_equal' => [SqlOperatorEnum::GTE, 5, 5, true],
            'GTE_false' => [SqlOperatorEnum::GTE, 3, 5, false],
            'LT_true' => [SqlOperatorEnum::LT, 5, 10, true],
            'LT_false' => [SqlOperatorEnum::LT, 10, 5, false],
            'LTE_true_less' => [SqlOperatorEnum::LTE, 3, 5, true],
            'LTE_true_equal' => [SqlOperatorEnum::LTE, 5, 5, true],
            'LTE_false' => [SqlOperatorEnum::LTE, 10, 5, false],
            'EQ_true' => [SqlOperatorEnum::EQ, 5, 5, true],
            'EQ_false' => [SqlOperatorEnum::EQ, 10, 5, false],
            'NEQ_true' => [SqlOperatorEnum::NEQ, 10, 5, true],
            'NEQ_false' => [SqlOperatorEnum::NEQ, 5, 5, false],
        ];
    }
}
