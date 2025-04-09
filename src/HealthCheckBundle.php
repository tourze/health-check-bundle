<?php

namespace HealthCheckBundle;

use Laminas\Diagnostics\Check\CheckInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HealthCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(CheckInterface::class)
            ->addTag('health-check.check');
    }
}
