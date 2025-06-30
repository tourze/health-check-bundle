<?php

namespace HealthCheckBundle\Tests\Unit\DependencyInjection;

use HealthCheckBundle\DependencyInjection\HealthCheckExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HealthCheckExtensionTest extends TestCase
{
    private HealthCheckExtension $extension;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new HealthCheckExtension();
    }

    public function testLoadServices(): void
    {
        $container = new ContainerBuilder();

        $this->extension->load([], $container);

        // 检查资源配置是否被加载
        $resources = $container->getResources();
        $this->assertNotEmpty($resources);
    }

    public function testLoadWithEmptyConfig(): void
    {
        $container = new ContainerBuilder();

        $this->extension->load([], $container);

        $this->assertFalse($container->getParameterBag()->has('health_check.nonexistent_parameter'));
    }
}