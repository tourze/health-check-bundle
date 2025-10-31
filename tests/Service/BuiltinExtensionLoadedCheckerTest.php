<?php

namespace HealthCheckBundle\Tests\Service;

use HealthCheckBundle\Service\BuiltinExtensionLoadedChecker;
use HealthCheckBundle\Tests\Service\TestKernel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 */
#[CoversClass(BuiltinExtensionLoadedChecker::class)]
final class BuiltinExtensionLoadedCheckerTest extends TestCase
{
    private string $tempDir;

    private string $composerJsonPath;

    private KernelInterface $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        // 创建一个临时目录作为项目目录
        $this->tempDir = sys_get_temp_dir() . '/health-check-ext-test-' . uniqid();
        mkdir($this->tempDir, 0o755, true);

        // 创建一个测试用的Kernel实例
        $this->kernel = new TestKernel($this->tempDir);

        // 创建一个模拟的 composer.json 文件
        $this->composerJsonPath = $this->tempDir . '/composer.json';
    }

    protected function tearDown(): void
    {
        // 清理临时目录
        if (file_exists($this->composerJsonPath)) {
            unlink($this->composerJsonPath);
        }

        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
    }

    public function testGetLabel(): void
    {
        // 创建一个简单的 composer.json 文件
        file_put_contents($this->composerJsonPath, json_encode([
            'require' => [
                'php' => '^8.1',
            ],
        ]));

        $checker = new BuiltinExtensionLoadedChecker($this->kernel);
        $this->assertEquals('检查项目扩展依赖', $checker->getLabel());
    }

    public function testConstructorWithNoExtensions(): void
    {
        // 创建一个没有 ext- 依赖的 composer.json 文件
        file_put_contents($this->composerJsonPath, json_encode([
            'require' => [
                'php' => '^8.1',
                'symfony/http-kernel' => '^6.4',
            ],
        ]));

        $checker = new BuiltinExtensionLoadedChecker($this->kernel);

        // 使用反射获取扩展列表
        $reflectionObject = new \ReflectionObject($checker);
        $extensionsProperty = $reflectionObject->getProperty('extensions');
        $extensionsProperty->setAccessible(true);
        $extensions = $extensionsProperty->getValue($checker);
        $this->assertEmpty($extensions);
    }

    public function testConstructorWithExtensions(): void
    {
        // 创建一个包含 ext- 依赖的 composer.json 文件
        file_put_contents($this->composerJsonPath, json_encode([
            'require' => [
                'php' => '^8.1',
                'ext-json' => '*',
                'ext-pdo' => '*',
                'symfony/http-kernel' => '^6.4',
            ],
        ]));

        $checker = new BuiltinExtensionLoadedChecker($this->kernel);

        // 使用反射获取扩展列表
        $reflectionObject = new \ReflectionObject($checker);
        $extensionsProperty = $reflectionObject->getProperty('extensions');
        $extensionsProperty->setAccessible(true);
        $extensions = $extensionsProperty->getValue($checker);
        self::assertIsArray($extensions);
        $this->assertContains('json', $extensions);
        $this->assertContains('pdo', $extensions);
        $this->assertCount(2, $extensions);
    }

    public function testConstructorWithDuplicateExtensions(): void
    {
        // 创建一个包含重复 ext- 依赖的 composer.json 文件
        file_put_contents($this->composerJsonPath, json_encode([
            'require' => [
                'php' => '^8.1',
                'ext-json' => '*',
                'ext-mbstring' => '*',
                'symfony/http-kernel' => '^6.4',
            ],
        ]));

        $checker = new BuiltinExtensionLoadedChecker($this->kernel);

        // 使用反射获取扩展列表
        $reflectionObject = new \ReflectionObject($checker);
        $extensionsProperty = $reflectionObject->getProperty('extensions');
        $extensionsProperty->setAccessible(true);
        $extensions = $extensionsProperty->getValue($checker);
        self::assertIsArray($extensions);
        $this->assertContains('json', $extensions);
        $this->assertContains('mbstring', $extensions);
        $this->assertCount(2, $extensions); // 不同的扩展不会被去重
    }
}
