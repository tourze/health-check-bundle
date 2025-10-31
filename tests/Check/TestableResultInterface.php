<?php

namespace HealthCheckBundle\Tests\Check;

/**
 * 测试用的 Result 接口，包含测试辅助方法
 */
interface TestableResultInterface
{
    public function fetchOne(): mixed;

    // 测试辅助方法
    public function setTestValue(mixed $value): void;
}
