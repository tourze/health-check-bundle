<?php

namespace HealthCheckBundle\Tests\Unit\Enum;

use HealthCheckBundle\Enum\SqlOperatorEnum;
use PHPUnit\Framework\TestCase;

class SqlOperatorEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('>', SqlOperatorEnum::GT->value);
        $this->assertEquals('>=', SqlOperatorEnum::GTE->value);
        $this->assertEquals('<', SqlOperatorEnum::LT->value);
        $this->assertEquals('<=', SqlOperatorEnum::LTE->value);
        $this->assertEquals('=', SqlOperatorEnum::EQ->value);
        $this->assertEquals('!=', SqlOperatorEnum::NEQ->value);
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('大于', SqlOperatorEnum::GT->getLabel());
        $this->assertEquals('大于等于', SqlOperatorEnum::GTE->getLabel());
        $this->assertEquals('小于', SqlOperatorEnum::LT->getLabel());
        $this->assertEquals('小于等于', SqlOperatorEnum::LTE->getLabel());
        $this->assertEquals('等于', SqlOperatorEnum::EQ->getLabel());
        $this->assertEquals('不等于', SqlOperatorEnum::NEQ->getLabel());
    }

    public function testImplementsInterfaces(): void
    {
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, SqlOperatorEnum::GT);
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, SqlOperatorEnum::GT);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, SqlOperatorEnum::GT);
    }

    public function testAllCases(): void
    {
        $cases = SqlOperatorEnum::cases();
        
        $this->assertCount(6, $cases);
        $this->assertContains(SqlOperatorEnum::GT, $cases);
        $this->assertContains(SqlOperatorEnum::GTE, $cases);
        $this->assertContains(SqlOperatorEnum::LT, $cases);
        $this->assertContains(SqlOperatorEnum::LTE, $cases);
        $this->assertContains(SqlOperatorEnum::EQ, $cases);
        $this->assertContains(SqlOperatorEnum::NEQ, $cases);
    }
}