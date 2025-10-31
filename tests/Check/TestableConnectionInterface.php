<?php

namespace HealthCheckBundle\Tests\Check;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

/**
 * 测试用的 Connection 接口，包含测试辅助方法
 */
interface TestableConnectionInterface
{
    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     */
    public function executeQuery(string $sql, array $params = [], array $types = [], ?QueryCacheProfile $qcp = null): Result;

    // 测试辅助方法
    public function setTestResult(TestableResultInterface $result): void;

    public function setTestException(\Exception $exception): void;
}
