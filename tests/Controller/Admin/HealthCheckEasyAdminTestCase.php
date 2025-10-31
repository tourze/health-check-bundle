<?php

declare(strict_types=1);

namespace HealthCheckBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * Health Check Bundle定制基类，修复客户端上下文问题
 */
#[CoversClass(AbstractEasyAdminControllerTestCase::class)]
#[RunTestsInSeparateProcesses]
abstract class HealthCheckEasyAdminTestCase extends AbstractEasyAdminControllerTestCase
{
    /**
     * 子类可以重写此方法来确保编辑测试有足够的数据
     */
    protected function ensureEditTestDataExists(): void
    {
        // 默认不做任何事情，子类可以重写此方法
    }
}
