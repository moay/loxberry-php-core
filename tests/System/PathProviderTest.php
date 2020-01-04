<?php

namespace LoxBerry\Tests\System;

use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;
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
            ['LBSHTMLAUTHDIR', '/opt/loxberry/webfrontend/htmlauth/system'],
            ['LBSHTMLDIR', '/opt/loxberry/webfrontend/html/system'],
            ['LBSTEMPLATEDIR', '/opt/loxberry/templates/system'],
            ['LBSDATADIR', '/opt/loxberry/data/system'],
            ['LBSLOGDIR', '/opt/loxberry/log/system'],
            ['LBSTMPFSLOGDIR', '/opt/loxberry/log/system_tmpfs'],
            ['LBSCONFIGDIR', '/opt/loxberry/config/system'],
            ['LBSSBINDIR', '/opt/loxberry/sbin'],
            ['LBSBINDIR', '/opt/loxberry/bin'],
            ['LBSCOMMUNICATIONCACHEDIR', '/opt/loxberry/run/shm'],
            ['LBPHTMLAUTHDIR', '/opt/loxberry/webfrontend/htmlauth/plugins'],
            ['LBPHTMLDIR', '/opt/loxberry/webfrontend/html/plugins'],
            ['LBPTEMPLATEDIR', '/opt/loxberry/templates/plugins'],
            ['LBPDATADIR', '/opt/loxberry/data/plugins'],
            ['LBPLOGDIR', '/opt/loxberry/log/plugins'],
            ['LBPCONFIGDIR', '/opt/loxberry/config/plugins'],
            ['LBPBINDIR', '/opt/loxberry/bin/plugins'],
            ['LOG_DATABASE_FILE', '/opt/loxberry/log/system_tmpfs/logs_sqlite.dat'],
            ['PLUGIN_DATABASE_FILE', '/opt/loxberry/data/system/plugindatabase.dat'],
            ['REBOOT_REQUIRED_FILE', '/opt/loxberry/log/system_tmpfs/reboot.required'],
            ['CENTRAL_CONFIG_FILE', '/opt/loxberry/config/system/general.cfg'],
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
