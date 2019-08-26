<?php

namespace LoxBerry\Tests\System\PluginDatabase;

use LoxBerry\Exceptions\PluginDatabaseException;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use LoxBerry\System\Plugin\PluginDatabase;
use LoxBerry\System\Plugin\PluginInformation;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginDatabaseTest.
 */
class PluginDatabaseTest extends TestCase
{
    /**
     * @dataProvider expectedPluginInformation
     */
    public function testProvidesPluginData($pluginName, $method, $expectedValue)
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'plugindatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $information = $pluginDatabase->getPluginInformation($pluginName);
        $this->assertInstanceOf(PluginInformation::class, $information);
        $this->assertEquals($expectedValue, $information->{$method}(), 'Comparison failed for '.$method);
    }

    public function testThrowsExceptionIfDatabaseIsNotReadable()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'nonexistingDatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->expectException(PluginDatabaseException::class);
        $pluginDatabase->getPluginInformation('test');
    }

    /**
     * @dataProvider expectedPluginInformation
     */
    public function testProvidesSingleValuesIfProvided($pluginName, $method, $expectedValue)
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'plugindatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $information = $pluginDatabase->{$method}($pluginName);
        $this->assertEquals($expectedValue, $information, 'Comparison failed for '.$method);
    }

    public function testThrowsExceptionIfMagicGetterIsMisusedWithWrongMethod()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'plugindatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->expectException(PluginDatabaseException::class);
        $pluginDatabase->getSomeUnknownStuff('weather4lox');
    }

    public function testThrowsExceptionIfMagicGetterIsMisusedWithSetter()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->expectException(PluginDatabaseException::class);
        $pluginDatabase->setName('sensebox');
    }

    public function testThrowsExceptionIfMagicGetterIsMisusedWithWrongPlugin()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'plugindatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->expectException(PluginDatabaseException::class);
        $pluginDatabase->getName('unknownPlugin');
    }

    public function testThrowsExceptionIfMagicGetterIsMisusedWithoutPlugin()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->expectException(\InvalidArgumentException::class);
        $pluginDatabase->getName();
    }

    public function testIdentifiesInstalledPluginsCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_PLUGIN_DATABASE_FILE)
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'plugindatabase.dat');

        $pluginDatabase = new PluginDatabase($pathProviderMock);
        $this->assertTrue($pluginDatabase->isInstalledPlugin('sensebox'));
        $this->assertFalse($pluginDatabase->isInstalledPlugin('unknownPlugin'));
    }

    public function expectedPluginInformation()
    {
        return [
            ['weather4lox', 'getNumber', 1],
            ['weather4lox', 'getAuthorName', 'Michael Schlenstedt'],
            ['weather4lox', 'getAuthorEmail', 'Michael@loxberry.de'],
            ['weather4lox', 'getVersion', '4.6.0.2'],
            ['weather4lox', 'getName', 'weather4lox'],
            ['weather4lox', 'getFolderName', 'weather4lox'],
            ['weather4lox', 'getTitle', 'Weather 4 Loxone'],
            ['weather4lox', 'getUiVersion', '2.0'],
            ['weather4lox', 'getAutoUpdate', 3],
            ['weather4lox', 'getReleaseCfg', 'https://raw.githubusercontent.com/mschlenstedt/LoxBerry-Plugin-Weather4Lox/master/release.cfg'],
            ['weather4lox', 'getPreReleaseCfg', 'https://raw.githubusercontent.com/mschlenstedt/LoxBerry-Plugin-Weather4Lox/master/prerelease.cfg'],
            ['weather4lox', 'getLogLevel', 3],
            ['weather4lox', 'getChecksum', '9489b114c85717a2122c27bc80b0b9cf'],
            ['weather4lox', 'isLogLevelsEnabled', true],
            ['weather4lox', 'getIconPath', '/system/images/icons/weather4lox/icon_64.png'],

            ['sensebox', 'getNumber', 2],
            ['sensebox', 'getAuthorName', 'moay'],
            ['sensebox', 'getAuthorEmail', 'mv@moay.de'],
            ['sensebox', 'getVersion', '1.0'],
            ['sensebox', 'getName', 'sensebox'],
            ['sensebox', 'getFolderName', 'sensebox'],
            ['sensebox', 'getTitle', 'senseBox'],
            ['sensebox', 'getUiVersion', '2.0'],
            ['sensebox', 'getAutoUpdate', 0],
            ['sensebox', 'getReleaseCfg', null],
            ['sensebox', 'getPreReleaseCfg', null],
            ['sensebox', 'getLogLevel', null],
            ['sensebox', 'getChecksum', 'b30af7534dc5f3ac8fc1abb6a1aaa23e'],
            ['sensebox', 'isLogLevelsEnabled', false],
            ['sensebox', 'getIconPath', '/system/images/icons/sensebox/icon_64.png'],
        ];
    }
}
