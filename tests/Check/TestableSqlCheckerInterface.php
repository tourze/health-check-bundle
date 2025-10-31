<?php

namespace HealthCheckBundle\Tests\Check;

use HealthCheckBundle\Enum\SqlOperatorEnum;

/**
 * 测试用的 SqlChecker 接口，包含测试辅助方法
 */
interface TestableSqlCheckerInterface
{
    public function getName(): string;

    public function getCronExpression(): string;

    public function getSql(): string;

    public function getOperator(): SqlOperatorEnum;

    public function getCompareValue(): int;

    // 测试辅助方法
    public function setTestName(string $name): void;

    public function setTestCronExpression(string $cronExpression): void;

    public function setTestSql(string $sql): void;

    public function setTestOperator(SqlOperatorEnum $operator): void;

    public function setTestCompareValue(int $compareValue): void;

    public function getNameCallCount(): int;
}
