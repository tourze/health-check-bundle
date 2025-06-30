<?php

namespace HealthCheckBundle\Tests\Unit;

use HealthCheckBundle\HealthCheckBundle;
use Laminas\Diagnostics\Check\CheckInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HealthCheckBundleTest extends TestCase
{
    private HealthCheckBundle $bundle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bundle = new HealthCheckBundle();
    }

    public function testBuild(): void
    {
        $container = new ContainerBuilder();

        $this->bundle->build($container);

        $autoconfiguredInstanceof = $container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(CheckInterface::class, $autoconfiguredInstanceof);
        
        $definition = $autoconfiguredInstanceof[CheckInterface::class];
        $this->assertTrue($definition->hasTag('health-check.check'));
    }

    public function testBundleInheritance(): void
    {
        $this->assertInstanceOf(\Symfony\Component\HttpKernel\Bundle\Bundle::class, $this->bundle);
    }
}