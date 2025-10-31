<?php

declare(strict_types=1);

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * 健康检查菜单服务测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 无需特殊设置
    }

    public function testServiceInstance(): void
    {
        // 从容器中获取 AdminMenu 服务
        $adminMenu = self::getService(AdminMenu::class);

        // 测试 AdminMenu 能够正常实例化
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);

        // 验证构造函数参数正确设置
        $reflection = new \ReflectionClass($adminMenu);
        $property = $reflection->getProperty('linkGenerator');
        $property->setAccessible(true);
        $linkGenerator = $property->getValue($adminMenu);

        $this->assertInstanceOf(LinkGeneratorInterface::class, $linkGenerator);
    }
}
