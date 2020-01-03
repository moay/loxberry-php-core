<?php

namespace LoxBerry\Tests\Logging\Database;

use LoxBerry\Logging\Database\LogFileDatabase;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use PHPUnit\Framework\TestCase;

/**
 * Class LogFileDatabaseFactoryTest.
 */
class LogFileDatabaseFactoryTest extends TestCase
{
    const TEST_DB_FILE = __DIR__.'/test.dat';

    protected function setUp(): void
    {
        $this->removeTestDbFile();
    }

    protected function tearDown(): void
    {
        $this->removeTestDbFile();
    }

    public function testFactoryReturnsProperlyCreatedDataBase()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock
            ->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_LOG_DATABASE_FILE)
            ->willReturn(self::TEST_DB_FILE);

        $lowLevelExecutorMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelExecutorMock
            ->expects($this->once())
            ->method('getFileOwner')
            ->willReturn('loxberry');

        $factory = new LogFileDatabaseFactory($pathProviderMock, $lowLevelExecutorMock);
        $database = $factory->create();

        $this->assertFileExists(self::TEST_DB_FILE);
        $this->assertInstanceOf(LogFileDatabase::class, $database);
    }

    public function testFactoryChangesFileOwnerIfItIsntCorrect()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock
            ->expects($this->once())
            ->method('getPath')
            ->with(Paths::PATH_LOG_DATABASE_FILE)
            ->willReturn(self::TEST_DB_FILE);

        $lowLevelExecutorMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelExecutorMock
            ->expects($this->once())
            ->method('getFileOwner')
            ->willReturn('test');
        $lowLevelExecutorMock
            ->expects($this->once())
            ->method('setFileOwner')
            ->with(self::TEST_DB_FILE, 'loxberry')
            ->willReturn(true);

        $factory = new LogFileDatabaseFactory($pathProviderMock, $lowLevelExecutorMock);
        $database = $factory->create();

        $this->assertFileExists(self::TEST_DB_FILE);
    }

    private function removeTestDbFile()
    {
        if (file_exists(self::TEST_DB_FILE)) {
            unlink(self::TEST_DB_FILE);
        }
    }
}
