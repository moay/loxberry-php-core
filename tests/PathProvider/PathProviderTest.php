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

        $pathProvider = new PathProvider($lowLevelMock);
        $this->assertEquals($expectedDirectory, $pathProvider->getPath($pathName));
    }

    public function exptectedDirectoriesDataProvider()
    {
        return [
            ['LBHOMEDIR', '/opt/loxberry'],
        ];
    }

    // Todo: Test env variable and user info mapping for LBHOMEDIR as well as logging
}
