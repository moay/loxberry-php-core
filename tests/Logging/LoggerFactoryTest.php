<?php

namespace LoxBerry\Tests\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\System\PathProvider;
use LoxBerry\System\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerFactoryTest.
 */
class LoggerFactoryTest extends TestCase
{
    public function testLoggerFactoryReturnsProperlyInitializedLogger()
    {
        $databaseFactoryMock = $this->createMock(LogFileDatabaseFactory::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $pathProviderMock = $this->createMock(PathProvider::class);
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $this->markTestIncomplete();
    }

    public function testLogStartHeadersAreProperlyLogged()
    {
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
}
