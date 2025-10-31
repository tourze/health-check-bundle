<?php

declare(strict_types=1);

namespace HealthCheckBundle\Tests;

use HealthCheckBundle\HealthCheckBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(HealthCheckBundle::class)]
#[RunTestsInSeparateProcesses]
final class HealthCheckBundleTest extends AbstractBundleTestCase
{
}
