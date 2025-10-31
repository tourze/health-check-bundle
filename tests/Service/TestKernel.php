<?php

namespace HealthCheckBundle\Tests\Service;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * 简单的测试Kernel类，只实现必要的方法
 * @internal
 */
final class TestKernel implements KernelInterface
{
    public function __construct(private string $projectDir)
    {
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    // 其他方法抛出异常，因为测试中不会用到
    public function boot(): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function shutdown(): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function reboot(?string $warmupDir): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getBundles(): array
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getBundle(string $name): BundleInterface
    {
        throw new \RuntimeException('Not implemented');
    }

    public function locateResource(string $name): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getEnvironment(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isDebug(): bool
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getContainer(): ContainerInterface
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getStartTime(): float
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getCacheDir(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getBuildDir(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getLogDir(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getCharset(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function registerBundles(): iterable
    {
        throw new \RuntimeException('Not implemented');
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
