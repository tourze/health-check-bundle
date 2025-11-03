<?php

namespace HealthCheckBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Laminas\Diagnostics\Check\CheckInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class HealthCheckBundle extends Bundle implements BundleDependencyInterface
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(CheckInterface::class)
            ->addTag('health-check.check')
        ;
    }

    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
