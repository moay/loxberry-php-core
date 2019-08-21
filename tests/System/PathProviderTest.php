<?php

namespace LoxBerry\Tests\System;

use LoxBerry\System\PathProvider;
use LoxBerry\System\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class PathProviderTest.
 */
class PathProviderTest extends TestCase
{
    /**
     * @dataProvider exptectedDirectoriesDataProvider
     */
    public function testDirectoriesAreProvidedCorrectly($pathName, $expectedDirectory)
    {
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);
        $lowLevelMock->method('errorLog')
            ->willReturn(true);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals($expectedDirectory, $pathProvider->getPath($pathName));
    }

    public function exptectedDirectoriesDataProvider()
    {
        return [
            ['LBHOMEDIR', '/opt/loxberry'],
            ['LOG_DATABASE_FILE', '/opt/loxberry/log/system_tmpfs/logs_sqlite.dat'],
            ['PLUGIN_DATABASE_FILE', '/opt/loxberry/data/system/plugindatabase.dat'],
        ];
    }

    public function testThrowsExceptionIfUnkownPathRequested()
    {
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);
        $lowLevelMock->method('errorLog')
            ->willReturn(true);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->expectException(\InvalidArgumentException::class);
        $pathProvider->getPath('UNKNOWN');
    }

    public function testHomeDirCanBeSetViaEnvironmentVariable()
    {
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn('TestPath');
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals('TestPath', $pathProvider->getPath('LBHOMEDIR'));
    }

    public function testHomeDirWillBeLoadedFromSystemUser()
    {
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(['uid' => ['dir' => 'TestPath2']]);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals('TestPath2', $pathProvider->getPath('LBHOMEDIR'));
    }

    public function testFallingBackToBasePathWillBeLogged()
    {
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);
        $lowLevelMock->method('errorLog')
            ->willReturn(true)
            ->willThrowException(new \Exception('Testing the logger'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Testing the logger');

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals('TestPath', $pathProvider->getPath('LBHOMEDIR'));
    }
}
