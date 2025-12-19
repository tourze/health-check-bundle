<?php

declare(strict_types=1);

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\BuiltinDirPermissionChecker;
use Laminas\Diagnostics\Result\ResultInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(BuiltinDirPermissionChecker::class)]
#[RunTestsInSeparateProcesses]
final class BuiltinDirPermissionCheckerTest extends AbstractIntegrationTestCase
{
    private BuiltinDirPermissionChecker $checker;

    protected function onSetUp(): void
    {
        // 创建测试所需的 data 目录
        $projectDir = self::getContainer()->getParameter('kernel.project_dir');
        self::assertIsString($projectDir);
        $dataDir = $projectDir . '/data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0o755, true);
        }

        $checker = self::getContainer()->get(BuiltinDirPermissionChecker::class);
        self::assertInstanceOf(BuiltinDirPermissionChecker::class, $checker);
        $this->checker = $checker;
    }

    public function testServiceInstanceFromContainer(): void
    {
        self::assertInstanceOf(BuiltinDirPermissionChecker::class, $this->checker);
    }

    public function testGetLabel(): void
    {
        self::assertEquals('检查基础目录权限', $this->checker->getLabel());
    }

    public function testCheckReturnsResult(): void
    {
        $result = $this->checker->check();
        self::assertInstanceOf(ResultInterface::class, $result);
    }

    public function testCheckerConfigurationViaReflection(): void
    {
        $reflection = new \ReflectionClass($this->checker);
        $parent = $reflection->getParentClass();

        self::assertNotFalse($parent);
        self::assertEquals('Laminas\\Diagnostics\\Check\\DirWritable', $parent->getName());
    }

    public function testCheckerDirectoriesConfiguration(): void
    {
        // Verify the checker has directories configured via reflection
        $reflection = new \ReflectionClass($this->checker);

        // DirWritable stores directories in 'dir' property
        $parentClass = $reflection->getParentClass();
        self::assertNotFalse($parentClass);

        $dirProperty = $parentClass->getProperty('dir');
        $dirProperty->setAccessible(true);
        $dirs = $dirProperty->getValue($this->checker);

        // Should have at least 3 directories: cache, logs, data
        self::assertIsArray($dirs);
        self::assertCount(3, $dirs);

        // Verify all directories exist in the configuration
        foreach ($dirs as $dir) {
            self::assertIsString($dir);
        }
    }
}
