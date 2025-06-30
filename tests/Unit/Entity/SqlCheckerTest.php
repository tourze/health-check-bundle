<?php

namespace HealthCheckBundle\Tests\Unit\Entity;

use HealthCheckBundle\Entity\SqlChecker;
use HealthCheckBundle\Enum\SqlOperatorEnum;
use PHPUnit\Framework\TestCase;

class SqlCheckerTest extends TestCase
{
    private SqlChecker $sqlChecker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlChecker = new SqlChecker();
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
        $result = $this->sqlChecker->__toString();
        
        $this->assertEquals('SqlChecker[0]: Unnamed', $result);
    }

    public function testFluentInterface(): void
    {
        $result = $this->sqlChecker
            ->setName('Fluent Test')
            ->setSql('SELECT 1')
            ->setCronExpression('0 * * * * *')
            ->setOperator(SqlOperatorEnum::GTE)
            ->setCompareValue(10)
            ->setRemark('Fluent interface test')
            ->setValid(true);

        $this->assertInstanceOf(SqlChecker::class, $result);
        $this->assertEquals('Fluent Test', $this->sqlChecker->getName());
        $this->assertEquals('SELECT 1', $this->sqlChecker->getSql());
        $this->assertEquals('0 * * * * *', $this->sqlChecker->getCronExpression());
        $this->assertEquals(SqlOperatorEnum::GTE, $this->sqlChecker->getOperator());
        $this->assertEquals(10, $this->sqlChecker->getCompareValue());
        $this->assertEquals('Fluent interface test', $this->sqlChecker->getRemark());
        $this->assertTrue($this->sqlChecker->isValid());
    }
}