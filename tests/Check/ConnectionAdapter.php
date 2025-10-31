<?php

namespace HealthCheckBundle\Tests\Check;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Result;

/**
 * Connection适配器，将TestableConnectionInterface适配为Connection
 */
class ConnectionAdapter extends Connection
{
    private TestableConnectionInterface $testableConnection;

    /**
     * @param array{driver?: 'ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv', memory?: bool} $params
     */
    public function __construct(array $params, Driver $driver, ?Configuration $config = null, ?TestableConnectionInterface $testableConnection = null)
    {
        parent::__construct($params, $driver, $config);
        if (null === $testableConnection) {
            throw new \InvalidArgumentException('TestableConnectionInterface is required');
        }
        $this->testableConnection = $testableConnection;
    }

    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, mixed> $types
     */
    public function executeQuery(string $sql, array $params = [], array $types = [], ?QueryCacheProfile $qcp = null): Result
    {
        // 直接委托给测试Connection，它会返回真实的Result
        return $this->testableConnection->executeQuery($sql, $params, $types, $qcp);
    }

    /**
     * 静态工厂方法用于创建测试实例
     */
    public static function createForTest(TestableConnectionInterface $testableConnection): self
    {
        $driver = new Driver\PDO\SQLite\Driver();

        return new self(['driver' => 'pdo_sqlite', 'memory' => true], $driver, null, $testableConnection);
    }
}
