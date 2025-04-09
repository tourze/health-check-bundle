<?php

namespace HealthCheckBundle\Service;

use Laminas\Diagnostics\Check\ExtensionLoaded;
use Symfony\Component\HttpKernel\KernelInterface;

class BuiltinExtensionLoadedChecker extends ExtensionLoaded
{
    public function __construct(KernelInterface $kernel)
    {
        $extensions = [];

        // 从composer.json拉依赖
        $composer = json_decode(file_get_contents($kernel->getProjectDir() . '/composer.json'), true);
        foreach ($composer['require'] ?? [] as $extensionName => $version) {
            if (str_starts_with($extensionName, 'ext-')) {
                $extensionName = substr($extensionName, 4);
                $extensions[] = $extensionName;
            }
        }
        $extensions = array_values(array_unique($extensions));

        parent::__construct($extensions);
    }

    public function getLabel(): string
    {
        return '检查项目扩展依赖';
    }
}
