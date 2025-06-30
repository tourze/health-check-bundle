<?php

namespace HealthCheckBundle\Service;

use Laminas\Diagnostics\Check\DirWritable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BuiltinDirPermissionChecker extends DirWritable
{
    public function __construct(
        #[Autowire(param: '%kernel.cache_dir%')]
        string $cacheDir,
        #[Autowire(param: '%kernel.logs_dir%')]
        string $logDir,
        #[Autowire(param: '%kernel.project_dir%/data')]
        string $dataDir,
    )
    {
        parent::__construct([
            $cacheDir,
            $logDir,
            $dataDir,
        ]);
    }

    public function getLabel(): string
    {
        return '检查基础目录权限';
    }
}
