<?php

namespace LoxBerry\Tests\PathProvider;

use LoxBerry\System\PathProvider;
use LoxBerry\Utility\LowLevel;
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
        $lowLevelMock = $this->createMock(LowLevel::class);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals($expectedDirectory, $pathProvider->getPath($pathName));
    }

    public function exptectedDirectoriesDataProvider()
    {
        return [
            ['LBHOMEDIR', '/opt/loxberry'],
        ];
    }

    public function testHomeDirCanBeSetViaEnvironmentVariable()
    {
        $lowLevelMock = $this->createMock(LowLevel::class);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn('TestPath');
        $lowLevelMock->method('getUserInfo')
            ->willReturn(null);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals('TestPath', $pathProvider->getPath('LBHOMEDIR'));
    }

    public function testHomeDirWillBeLoadedFromSystemUser()
    {
        $lowLevelMock = $this->createMock(LowLevel::class);
        $lowLevelMock->method('getEnvironmentVariable')
            ->willReturn(null);
        $lowLevelMock->method('getUserInfo')
            ->willReturn(['uid' => ['dir' => 'TestPath2']]);

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals('TestPath2', $pathProvider->getPath('LBHOMEDIR'));
    }

    // Todo: Test env variable and user info mapping for LBHOMEDIR as well as logging
}
