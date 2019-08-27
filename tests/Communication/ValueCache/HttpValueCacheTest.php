<?php

namespace LoxBerry\Tests\Communication\ValueCache;

use LoxBerry\Communication\ValueCache\HttpValueCache;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpValueCacheTest.
 */
class HttpValueCacheTest extends TestCase
{
    const TEST_CACHE_DIR = __DIR__.'/tmp_http';

    public function testStoresValuesCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_CACHE_DIR);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $cache = new HttpValueCache($pathProviderMock, $lowLevelMock);
        $cache->put('testkey', 'testvalue', '123.123.0.1', 9000);
        $content = file_get_contents(self::TEST_CACHE_DIR.'/mshttp_mem_'.md5('123.123.0.1').'.json');

        $this->assertStringContainsString('"testkey": "testvalue"', $content);
    }

    public function testReturnsStoredValuesCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_CACHE_DIR);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $cache = new HttpValueCache($pathProviderMock, $lowLevelMock);
        $this->assertNull($cache->get('testkey', '123.123.0.1', 9000));
        $cache->put('testkey', 'testvalue', '123.123.0.1', 9000);
        $content = file_get_contents(self::TEST_CACHE_DIR.'/mshttp_mem_'.md5('123.123.0.1').'.json');
        $this->assertStringContainsString('"testkey": "testvalue"', $content);
        $this->assertSame('testvalue', $cache->get('testkey', '123.123.0.1', 9000));
    }

    public function testIdentifiesValueStateCorrectly()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_CACHE_DIR);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $cache = new HttpValueCache($pathProviderMock, $lowLevelMock);
        $cache->put('testkey', 'testvalue', '123.123.0.1', 9000);

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
