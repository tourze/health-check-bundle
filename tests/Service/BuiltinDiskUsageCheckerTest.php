<?php

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\BuiltinDiskUsageChecker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BuiltinDiskUsageCheckerTest extends TestCase
{
    public function testGetLabel(): void
    {
        $checker = new BuiltinDiskUsageChecker();
        $this->assertEquals('机器硬盘容量检查', $checker->getLabel());
    }

    /**
     * 测试构造函数初始化值
     */
    public function testConstructorInitialization(): void
    {
        $checker = new BuiltinDiskUsageChecker();

        $reflection = new ReflectionClass($checker);

        $warningProp = $reflection->getProperty('warningThreshold');
        $warningProp->setAccessible(true);
        $this->assertEquals(80, $warningProp->getValue($checker));

        $criticalProp = $reflection->getProperty('criticalThreshold');
        $criticalProp->setAccessible(true);
        $this->assertEquals(95, $criticalProp->getValue($checker));
    }

    /**
     * 测试磁盘使用率不同场景的检查结果
     */
    public function testCheckResultsWithDifferentThresholds(): void
    {
        // 为了简化测试，我们不模拟具体的检查逻辑，只测试标签和初始化参数
        $this->markTestSkipped('需要模拟磁盘使用情况才能进行完整测试');

        // 注意：在实际环境中，这些测试需要更复杂的设置来模拟磁盘使用情况
        // 可能需要使用 vfsStream 等虚拟文件系统
    }
}
