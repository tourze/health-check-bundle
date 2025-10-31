<?php

namespace HealthCheckBundle\Tests\Entity;

use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SqlChecker::class)]
final class SqlCheckerTest extends AbstractEntityTestCase
{
    protected function createEntity(): SqlChecker
    {
        $entity = new SqlChecker();
        $entity->setName('Test Checker');
        $entity->setSql('SELECT 1');
        $entity->setCronExpression('0 * * * * *');

        return $entity;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'Database Connection Check'];
        yield 'sql' => ['sql', 'SELECT COUNT(*) FROM users WHERE active = 1'];
        yield 'cronExpression' => ['cronExpression', '0 */5 * * * *'];
        yield 'operator' => ['operator', SqlOperatorEnum::GT];
        yield 'compareValue' => ['compareValue', 100];
        yield 'remark' => ['remark', 'This check monitors active user count'];
        yield 'valid' => ['valid', true];
    }

    private SqlChecker $sqlChecker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlChecker = new SqlChecker();
        $this->sqlChecker->setName('Test Checker');
        $this->sqlChecker->setSql('SELECT 1');
        $this->sqlChecker->setCronExpression('0 * * * * *');
    }

    public function testGetSetName(): void
    {
        $name = 'Database Connection Check';
        $this->sqlChecker->setName($name);

        $this->assertEquals($name, $this->sqlChecker->getName());
    }

    public function testGetSetSql(): void
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE active = 1';
        $this->sqlChecker->setSql($sql);

        $this->assertEquals($sql, $this->sqlChecker->getSql());
    }

    public function testGetSetCronExpression(): void
    {
        $cronExpression = '0 */5 * * * *';
        $this->sqlChecker->setCronExpression($cronExpression);

        $this->assertEquals($cronExpression, $this->sqlChecker->getCronExpression());
    }

    public function testGetSetOperator(): void
    {
        $operator = SqlOperatorEnum::GT;
        $this->sqlChecker->setOperator($operator);

        $this->assertEquals($operator, $this->sqlChecker->getOperator());
    }

    public function testDefaultOperator(): void
    {
        $this->assertEquals(SqlOperatorEnum::EQ, $this->sqlChecker->getOperator());
    }

    public function testGetSetCompareValue(): void
    {
        $compareValue = 100;
        $this->sqlChecker->setCompareValue($compareValue);

        $this->assertEquals($compareValue, $this->sqlChecker->getCompareValue());
    }

    public function testDefaultCompareValue(): void
    {
        $this->assertEquals(0, $this->sqlChecker->getCompareValue());
    }

    public function testGetSetRemark(): void
    {
        $remark = 'This check monitors active user count';
        $this->sqlChecker->setRemark($remark);

        $this->assertEquals($remark, $this->sqlChecker->getRemark());
    }

    public function testNullRemark(): void
    {
        $this->assertNull($this->sqlChecker->getRemark());
    }

    public function testGetSetValid(): void
    {
        $this->sqlChecker->setValid(true);
        $this->assertTrue($this->sqlChecker->isValid());

        $this->sqlChecker->setValid(false);
        $this->assertFalse($this->sqlChecker->isValid());
    }

    public function testDefaultValid(): void
    {
        $this->assertFalse($this->sqlChecker->isValid());
    }

    public function testToString(): void
    {
        $this->sqlChecker->setName('Test Checker');

        $result = $this->sqlChecker->__toString();

        $this->assertEquals('SqlChecker[0]: Test Checker', $result);
    }

    public function testToStringWithoutName(): void
    {
        // Create a new entity with empty name to test empty name behavior
        $unnamedChecker = new SqlChecker();
        $result = $unnamedChecker->__toString();

        // Empty string is not null, so it doesn't show as "Unnamed"
        $this->assertEquals('SqlChecker[0]: ', $result);
    }

    public function testFluentInterface(): void
    {
        // Test that all setters work correctly (adapted from fluent interface test)
        $this->sqlChecker->setName('Fluent Test');
        $this->sqlChecker->setSql('SELECT 1');
        $this->sqlChecker->setCronExpression('0 * * * * *');
        $this->sqlChecker->setOperator(SqlOperatorEnum::GTE);
        $this->sqlChecker->setCompareValue(10);
        $this->sqlChecker->setRemark('Fluent interface test');
        $this->sqlChecker->setValid(true);

        // Verify all values were set correctly
        $this->assertInstanceOf(SqlChecker::class, $this->sqlChecker);
        $this->assertEquals('Fluent Test', $this->sqlChecker->getName());
        $this->assertEquals('SELECT 1', $this->sqlChecker->getSql());
        $this->assertEquals('0 * * * * *', $this->sqlChecker->getCronExpression());
        $this->assertEquals(SqlOperatorEnum::GTE, $this->sqlChecker->getOperator());
        $this->assertEquals(10, $this->sqlChecker->getCompareValue());
        $this->assertEquals('Fluent interface test', $this->sqlChecker->getRemark());
        $this->assertTrue($this->sqlChecker->isValid());
    }
}
