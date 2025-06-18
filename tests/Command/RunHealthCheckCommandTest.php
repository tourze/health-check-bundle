<?php

namespace HealthCheckBundle\Tests\Command;

use HealthCheckBundle\Command\RunHealthCheckCommand;
use HealthCheckBundle\Service\CheckerService;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunHealthCheckCommandTest extends TestCase
{
    /**
     * @var CheckerService&MockObject
     */
    private $checkerService;

    /**
     * @var RunHealthCheckCommand
     */
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkerService = $this->createMock(CheckerService::class);
        $this->command = new RunHealthCheckCommand($this->checkerService);
    }

    public function testCommandInitialization(): void
    {
        // 测试命令基本属性
        $this->assertEquals('health-check:run', $this->command->getName());
        $this->assertEquals('运行所有健康检查', $this->command->getDescription());
    }

    public function testExecuteWithNoCheckers(): void
    {
        // 模拟CheckerService返回空数组
        $this->checkerService->expects($this->once())
            ->method('getCheckers')
            ->willReturn([]);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        // 验证输出包含警告信息
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('没有找到可用的健康检查器', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithSuccessfulCheckers(): void
    {
        // 创建成功的checker mock
        $successChecker = $this->createMock(CheckInterface::class);
        $successChecker->expects($this->once())
            ->method('check')
            ->willReturn(new Success('Check passed'));
        $successChecker->expects($this->any())
            ->method('getLabel')
            ->willReturn('Test Success Checker');

        $this->checkerService->expects($this->once())
            ->method('getCheckers')
            ->willReturn([$successChecker]);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        // 验证返回状态码为成功
        $this->assertEquals(0, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始执行健康检查', $output);
    }

    public function testExecuteWithFailingCheckers(): void
    {
        // 创建失败的checker mock
        $failChecker = $this->createMock(CheckInterface::class);
        $failChecker->expects($this->once())
            ->method('check')
            ->willReturn(new Failure('Check failed'));
        $failChecker->expects($this->any())
            ->method('getLabel')
            ->willReturn('Test Fail Checker');

        $this->checkerService->expects($this->once())
            ->method('getCheckers')
            ->willReturn([$failChecker]);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        // 验证返回状态码为失败
        $this->assertEquals(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始执行健康检查', $output);
    }
}
