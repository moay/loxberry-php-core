<?php

namespace LoxBerry\Tests\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Database\LogFileDatabase;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\LoggerFactory;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Plugin\PluginDatabase;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerFactoryTest.
 */
class LoggerFactoryTest extends TestCase
{
    const TEST_FILE = 'testLogFile.log';

    public function testLoggerFactoryReturnsProperlyInitializedLogger()
    {
        $databaseFactoryMock = $this->createMock(LogFileDatabaseFactory::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $pathProviderMock = $this->createMock(PathProvider::class);
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $loggerFactory = new LoggerFactory(
            $databaseFactoryMock,
            $lowLevelMock,
            $pathProviderMock,
            $systemConfigurationMock,
            $pluginDatabaseMock
        );

        $logger = $loggerFactory->create('eventLog', 'testPlugin', null, false);
        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testLogStartHeadersAreProperlyLogged()
    {
        $databaseFactoryMock = $this->createMock(LogFileDatabaseFactory::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $pathProviderMock = $this->createMock(PathProvider::class);
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getLoxBerryVersion')
            ->willReturn('1.5.0');

        $loggerFactory = new LoggerFactory(
            $databaseFactoryMock,
            $lowLevelMock,
            $pathProviderMock,
            $systemConfigurationMock,
            $pluginDatabaseMock
        );

        $logger = $loggerFactory->create('eventLog', 'testPlugin', __DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logger->log('Test');
        $loggedStuff = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $this->assertStringContainsString('1.5.0', $loggedStuff);
    }

    public function testLogStartDatabaseQueriesAreProperlyExecutedIfFileWritingIsEnabled()
    {
        $databaseFactoryMock = $this->createMock(LogFileDatabaseFactory::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $pathProviderMock = $this->createMock(PathProvider::class);
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $databaseMock = $this->createMock(LogFileDatabase::class);
        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $databaseMock->expects($this->once())
            ->method('logStart')
            ->with('testPlugin', 'eventLog', __DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);

        $databaseFactoryMock
            ->method('create')
            ->willReturn($databaseMock);

        $loggerFactory = new LoggerFactory(
            $databaseFactoryMock,
            $lowLevelMock,
            $pathProviderMock,
            $systemConfigurationMock,
            $pluginDatabaseMock
        );

        $logger = $loggerFactory->create('eventLog', 'testPlugin', __DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logger->log('test');
    }

    protected function setUp(): void
    {
        $this->removeTestFile();
    }

    protected function tearDown(): void
    {
        $this->removeTestFile();
    }

    private function removeTestFile()
    {
        if (file_exists(__DIR__.'/'.self::TEST_FILE)) {
            unlink(__DIR__.'/'.self::TEST_FILE);
        }
    }
}
