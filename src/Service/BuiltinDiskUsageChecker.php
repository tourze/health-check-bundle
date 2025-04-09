<?php

namespace HealthCheckBundle\Service;

use Laminas\Diagnostics\Check\DiskUsage;

/**
 * 机器硬盘容量检查
 * TODO 对于集群环境，这个是不足够的，需要检查每个Pod的硬盘容量情况
 */
class BuiltinDiskUsageChecker extends DiskUsage
{
    public function __construct()
    {
        parent::__construct(80, 95);
    }

    public function getLabel(): string
    {
        return '机器硬盘容量检查';
    }
}
