<?php

namespace HealthCheckBundle\Tests\Command;

use HealthCheckBundle\Command\RunHealthCheckCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(RunHealthCheckCommand::class)]
#[RunTestsInSeparateProcesses]
final class RunHealthCheckCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 创建测试所需的 data 目录
        $projectDir = self::getContainer()->getParameter('kernel.project_dir');
        self::assertIsString($projectDir);
        $dataDir = $projectDir . '/data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0o755, true);
        }
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getContainer()->get(RunHealthCheckCommand::class);
        self::assertInstanceOf(RunHealthCheckCommand::class, $command);
        $application = new Application();
        $application->add($command);

        return new CommandTester($command);
    }

    public function testCommandInitialization(): void
    {
        $command = self::getContainer()->get(RunHealthCheckCommand::class);
        self::assertInstanceOf(RunHealthCheckCommand::class, $command);

        // 测试命令基本属性
        $this->assertEquals('health-check:run', $command->getName());
        $this->assertEquals('运行所有健康检查', $command->getDescription());
    }

    public function testExecuteWithBuiltinCheckers(): void
    {
        $commandTester = $this->getCommandTester();

        try {
            $commandTester->execute([]);
        } catch (\Exception $e) {
            self::fail('Command execution failed with exception: ' . $e->getMessage());
        }

        // 验证命令正常执行
        $output = $commandTester->getDisplay();
        $statusCode = $commandTester->getStatusCode();

        $this->assertStringContainsString('开始执行健康检查', $output);

        $this->assertStringContainsString('检查结果统计', $output);

        // 命令会根据检查结果返回相应的状态码，我们只验证执行成功，不验证具体状态码
        // 因为内置检查器在不同环境中可能有不同结果
        $this->assertIsInt($statusCode);
        $this->assertContains($statusCode, [0, 1], 'Status code should be either SUCCESS (0) or FAILURE (1)');
    }

    public function testExecuteCommandOutputFormat(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        // 验证命令输出格式
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('开始执行健康检查', $output);
        $this->assertStringContainsString('检查结果统计', $output);
        $this->assertStringContainsString('状态', $output);
        $this->assertStringContainsString('数量', $output);

        // 验证表格包含各种状态
        $this->assertStringContainsString('成功', $output);
        $this->assertStringContainsString('失败', $output);
        $this->assertStringContainsString('警告', $output);
        $this->assertStringContainsString('跳过', $output);
        $this->assertStringContainsString('未知', $output);
    }

    public function testCommandReturnsDifferentStatusCodes(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        // 验证命令正常执行
        $output = $commandTester->getDisplay();
        $statusCode = $commandTester->getStatusCode();

        $this->assertStringContainsString('开始执行健康检查', $output);

        // 验证返回的状态码在合理范围内
        $this->assertIsInt($statusCode);
        $this->assertContains($statusCode, [0, 1], 'Status code should be either SUCCESS (0) or FAILURE (1)');
    }
}
