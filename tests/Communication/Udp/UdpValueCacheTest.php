<?php

namespace LoxBerry\Tests\Communication\Udp;

use LoxBerry\Communication\Udp\UdpValueCache;
use LoxBerry\System\PathProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class UdpValueCacheTest.
 */
class UdpValueCacheTest extends TestCase
{
    const TEST_CACHE_DIR = __DIR__.'/tmp';

    public function testStoresValuesCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_CACHE_DIR);

        $cache = new UdpValueCache($pathProviderMock);
        $cache->storeValue('testkey', 'testvalue', '123.123.0.1', 9000);
        $content = file_get_contents(self::TEST_CACHE_DIR.'/msudp_mem_'.md5('123.123.0.1').'_9000.json');
        $this->assertStringContainsString('"testkey":"testvalue"', $content);
    }

    public function testIdentifiesValueStateCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_CACHE_DIR);

        $cache = new UdpValueCache($pathProviderMock);
        $cache->storeValue('testkey', 'testvalue', '123.123.0.1', 9000);

        $this->assertFalse($cache->valueDiffersFromStored('testkey', 'testvalue', '123.123.0.1', 9000));
        $this->assertTrue($cache->valueDiffersFromStored('testkey', 'differingValue', '123.123.0.1', 9000));
    }

    protected function setUp(): void
    {
        $this->removeTestFilesFolder();
    }

    protected function tearDown(): void
    {
        $this->removeTestFilesFolder();
    }

    private function removeTestFilesFolder()
    {
        if (file_exists(self::TEST_CACHE_DIR)) {
            $this->deleteFolderRecursively(self::TEST_CACHE_DIR);
        }
    }

    private function deleteFolderRecursively($dir)
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            if (is_dir("$dir/$file")) {
                $this->deleteFolderRecursively("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }
        rmdir($dir);
    }
}
