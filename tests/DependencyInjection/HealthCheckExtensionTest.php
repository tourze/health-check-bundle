<?php

namespace HealthCheckBundle\Tests\DependencyInjection;

use HealthCheckBundle\DependencyInjection\HealthCheckExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(HealthCheckExtension::class)]
final class HealthCheckExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private HealthCheckExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new HealthCheckExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    protected function getExtension(): HealthCheckExtension
    {
        return $this->extension;
    }

    public function testLoadServices(): void
    {
        $this->extension->load([], $this->container);

        // 检查资源配置是否被加载
        $resources = $this->container->getResources();
        $this->assertNotEmpty($resources);
    }

    public function testLoadWithEmptyConfig(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->extension->load([], $this->container);

        $this->assertFalse($this->container->getParameterBag()->has('health_check.nonexistent_parameter'));
    }
}
