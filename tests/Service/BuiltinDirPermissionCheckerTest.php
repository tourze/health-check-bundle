<?php

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\BuiltinDirPermissionChecker;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use PHPUnit\Framework\TestCase;

class BuiltinDirPermissionCheckerTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建临时目录
        $this->tempDir = sys_get_temp_dir() . '/health-check-test-' . uniqid();
        mkdir($this->tempDir, 0755, true);

        // 创建模拟目录
        mkdir($this->tempDir . '/cache', 0755);
        mkdir($this->tempDir . '/logs', 0755);
        mkdir($this->tempDir . '/data', 0755);
    }

    protected function tearDown(): void
    {
        // 清理临时目录
        $this->removeDirectory($this->tempDir);

        parent::tearDown();
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }

    public function testGetLabel(): void
    {
        $checker = new BuiltinDirPermissionChecker(
            $this->tempDir . '/cache',
            $this->tempDir . '/logs',
            $this->tempDir . '/data'
        );

        $this->assertEquals('检查基础目录权限', $checker->getLabel());
    }

    public function testCheckWithWritableDirs(): void
    {
        $checker = new BuiltinDirPermissionChecker(
            $this->tempDir . '/cache',
            $this->tempDir . '/logs',
            $this->tempDir . '/data'
        );

        $result = $checker->check();

        $this->assertInstanceOf(Success::class, $result);
    }

    public function testCheckWithNonWritableDir(): void
    {
        // 修改目录权限为只读
        chmod($this->tempDir . '/logs', 0444);

        $checker = new BuiltinDirPermissionChecker(
            $this->tempDir . '/cache',
            $this->tempDir . '/logs',
            $this->tempDir . '/data'
        );

        $result = $checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertStringContainsString($this->tempDir . '/logs', $result->getMessage());
    }

    public function testCheckWithNonExistentDir(): void
    {
        $nonExistentDir = $this->tempDir . '/non-existent';

        $checker = new BuiltinDirPermissionChecker(
            $this->tempDir . '/cache',
            $this->tempDir . '/logs',
            $nonExistentDir
        );

        $result = $checker->check();

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertStringContainsString($nonExistentDir, $result->getMessage());
    }
}
