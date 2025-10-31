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
        $composerPath = $kernel->getProjectDir() . '/composer.json';
        if (!file_exists($composerPath)) {
            parent::__construct([]);

            return;
        }

        $composerContent = file_get_contents($composerPath);
        if (false === $composerContent) {
            parent::__construct([]);

            return;
        }

        $composer = json_decode($composerContent, true);
        if (!is_array($composer)) {
            parent::__construct([]);

            return;
        }

        $require = $composer['require'] ?? [];
        if (!is_array($require)) {
            parent::__construct([]);

            return;
        }

        foreach ($require as $extensionName => $version) {
            if (is_string($extensionName) && str_starts_with($extensionName, 'ext-')) {
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
