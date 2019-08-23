<?php

namespace LoxBerry\Tests\System\Plugin;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use LoxBerry\System\Plugin\PluginPathProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginPathProviderTest.
 */
class PluginPathProviderTest extends TestCase
{
    /**
     * @dataProvider expectedPluginPaths
     */
    public function testResolvesPluginPathsCorrectly($pluginName, $pathName, $baseDirectory, $expectedPath)
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with($baseDirectory)
            ->willReturn('/testable/directory');

        $pluginPathProvider = new PluginPathProvider($pathProviderMock);
        $pluginPathProvider->setPluginName($pluginName);
        $this->assertEquals(
            $expectedPath,
            $pluginPathProvider->getPath($pathName),
            sprintf('Assertion failed for path %s', $pathName)
        );
    }

    public function testThrowsExceptionIfPluginWasNotSet()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProvider = new PluginPathProvider($pathProviderMock);
        $this->expectException(\LogicException::class);
        $pluginPathProvider->getPath('something');
    }

    public function testThrowsExceptionIfUnknownPathRequested()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProvider = new PluginPathProvider($pathProviderMock);
        $pluginPathProvider->setPluginName('test');
        $this->expectException(\InvalidArgumentException::class);
        $pluginPathProvider->getPath('something');
    }

    public function expectedPluginPaths()
    {
        return [
            ['test', Paths::PATH_PLUGIN_HTMLAUTH, Paths::PATH_SYSTEM_HTMLAUTH, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_HTML, Paths::PATH_SYSTEM_HTML, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_TEMPLATE, Paths::PATH_SYSTEM_TEMPLATE, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_DATA, Paths::PATH_SYSTEM_DATA, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_LOG, Paths::PATH_SYSTEM_LOG, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_CONFIG, Paths::PATH_SYSTEM_CONFIG, '/testable/directory/plugins/test'],
            ['test', Paths::PATH_PLUGIN_BIN, Paths::PATH_SYSTEM_BIN, '/testable/directory/plugins/test'],
        ];
    }
}
