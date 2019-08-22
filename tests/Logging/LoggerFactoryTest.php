<?php

namespace LoxBerry\Tests\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\LoggerFactory;
use LoxBerry\System\PathProvider;
use LoxBerry\System\LowLevelExecutor;
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

        $loggerFactory = new LoggerFactory(
            $databaseFactoryMock,
            $lowLevelMock,
            $pathProviderMock,
            $systemConfigurationMock
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

        $loggerFactory = new LoggerFactory(
            $databaseFactoryMock,
            $lowLevelMock,
            $pathProviderMock,
            $systemConfigurationMock
        );

        $logger = $loggerFactory->create('eventLog', 'testPlugin', __DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $this->markTestIncomplete();
        // Logs LB version
        // Logs Package/Plugin title and version
        // Logs LogLevel
    }

    public function testLogStartDatabaseQueriesAreProperlyExecutedIfFileWritingIsEnabled()
    {
        $this->markTestIncomplete();
        // Test log to db if writing to file
        // Test logs sessino info to db including filename and package info
        // Test logs params [append, loglevel, loglevel_is_static, logdir, LOGSTARTBYTE, _ISPLUGIN, PLUGINTITLE, stdout, stderr]
    }

    public function testLogEndDatabaseQueriesAreProperlyExecutedIfFileWritingIsEnabled()
    {
        $this->markTestIncomplete();
        // Test log to db if writing to file
        // Test logs session info to db including filename and package info
        // Test logs params [append, loglevel, loglevel_is_static, logdir, LOGSTARTBYTE, _ISPLUGIN, PLUGINTITLE, stdout, stderr, ATTENTIONMESSAGES, STATUS]
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
