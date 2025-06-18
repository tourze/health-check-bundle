<?php

namespace HealthCheckBundle\Tests\Check;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use HealthCheckBundle\Check\SqlPdoChecker;
use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use PHPUnit\Framework\TestCase;

class SqlPdoCheckerTest extends TestCase
{
    /**
     * @var SqlChecker&MockObject
     */
    private $sqlChecker;

    /**
     * @var Connection&MockObject
     */
    private $connection;

    private SqlPdoChecker $checker;

    /**
     * @var Result&MockObject
     */
    private $result;

    protected function setUp(): void
    {
        $this->sqlChecker = $this->createMock(SqlChecker::class);
        $this->connection = $this->createMock(Connection::class);
        $this->result = $this->createMock(Result::class);
        $this->checker = new SqlPdoChecker($this->sqlChecker, $this->connection);
    }

    public function testGetLabel(): void
    {
        $this->sqlChecker->expects($this->once())
            ->method('getName')
            ->willReturn('测试SQL检查');

        $this->assertEquals('SQL检查: 测试SQL检查', $this->checker->getLabel());
    }

    public function testCheckWhenNotDue(): void
    {
        // 设置 cron 表达式为非当前时间
        $this->sqlChecker->expects($this->once())
            ->method('getCronExpression')
            ->willReturn('0 0 31 2 *'); // 2月31日，这一天不存在，所以永远不会到期

        $result = $this->checker->check();

        $this->assertInstanceOf(Skip::class, $result);
        $this->assertEquals('不在检查时间范围内', $result->getMessage());
    }

    public function testCheckWhenSqlFailsWithNoResult(): void
    {
        // 设置 cron 表达式为当前时间（每分钟）
        $this->sqlChecker->expects($this->once())
            ->method('getCronExpression')
            ->willReturn('* * * * *');

        $this->sqlChecker->expects($this->once())
            ->method('getSql')
            ->willReturn('SELECT COUNT(*) FROM users');

        $this->sqlChecker->expects($this->once())
            ->method('getName')
            ->willReturn('用户计数');

        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->willReturn($this->result);

        $this->result->expects($this->once())
            ->method('fetchOne')
            ->willReturn(false);

        $result = $this->checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('[用户计数] SQL执行失败: 未返回数据', $result->getMessage());
    }

    public function testCheckWhenSqlThrowsException(): void
    {
        // 设置 cron 表达式为当前时间
        $this->sqlChecker->expects($this->once())
            ->method('getCronExpression')
            ->willReturn('* * * * *');

        $this->sqlChecker->expects($this->once())
            ->method('getSql')
            ->willReturn('INVALID SQL');

        $this->sqlChecker->expects($this->once())
            ->method('getName')
            ->willReturn('无效SQL');

        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->willThrowException(new \Exception('SQL语法错误'));

        $result = $this->checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('[无效SQL] SQL执行失败: SQL语法错误', $result->getMessage());
    }

    /**
     * @dataProvider operatorDataProvider
     */
    public function testCheckWithDifferentOperators(
        SqlOperatorEnum $operator,
        int             $value,
        int             $compareValue,
        bool            $expectedValid
    ): void
    {
        // 设置 cron 表达式为当前时间
        $this->sqlChecker->expects($this->once())
            ->method('getCronExpression')
            ->willReturn('* * * * *');

        $this->sqlChecker->expects($this->once())
            ->method('getSql')
            ->willReturn('SELECT COUNT(*) FROM table');

        $this->sqlChecker->expects($this->once())
            ->method('getOperator')
            ->willReturn($operator);

        $this->sqlChecker->expects($this->once())
            ->method('getCompareValue')
            ->willReturn($compareValue);

        $this->sqlChecker->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('测试检查');

        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->willReturn($this->result);

        $this->result->expects($this->once())
            ->method('fetchOne')
            ->willReturn($value);

        $result = $this->checker->check();

        if ($expectedValid) {
            $this->assertInstanceOf(Success::class, $result);
            $this->assertStringContainsString('[测试检查] SQL检查通过', $result->getMessage());
        } else {
            $this->assertInstanceOf(Failure::class, $result);
            $this->assertStringContainsString('[测试检查] SQL检查未通过', $result->getMessage());
        }
    }

    public function operatorDataProvider(): array
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
