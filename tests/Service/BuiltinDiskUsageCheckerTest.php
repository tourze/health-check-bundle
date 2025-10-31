<?php

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\BuiltinDiskUsageChecker;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BuiltinDiskUsageChecker::class)]
final class BuiltinDiskUsageCheckerTest extends TestCase
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

        $reflection = new \ReflectionClass($checker);

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
        $checker = new BuiltinDiskUsageChecker();

        // 测试check方法返回的是Result对象
        $result = $checker->check();
        $this->assertInstanceOf(ResultInterface::class, $result);

        // 测试实际磁盘使用情况
        // 因为这是在真实环境中运行，我们只验证结果的类型
        $possibleResults = [
            Success::class,
            Warning::class,
            Failure::class,
        ];

        $resultClass = get_class($result);
        $this->assertContains($resultClass, $possibleResults,
            sprintf('Result should be one of Success/Warning/Failure, got %s', $resultClass));

        // 验证结果消息格式
        $message = $result->getMessage();
        $this->assertNotEmpty($message);

        // 消息应该包含百分比信息 - 注意匹配 "percent" 或 "%"
        $this->assertMatchesRegularExpression('/\d+(\.\d+)?(\s+percent|%)/', $message,
            'Result message should contain percentage');
    }
}
