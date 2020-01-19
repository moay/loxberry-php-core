<?php

namespace LoxBerry\Tests\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\Writer\LogSystemWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerTest.
 */
class LoggerTest extends TestCase
{
    /**
     * @param $logToStdErr
     * @param $logToStdOut
     * @param $logToFile
     * @param $minimumLogLevel
     *
     * @dataProvider logConfigurationTestDataProvider
     *
     * @throws \Exception
     */
    public function testLogConfigurationIsRespected($logToStdErr, $logToStdOut, $logToFile, $minimumLogLevel)
    {
        $eventLoggerMock = $this->createMock(Logger\EventLogger::class);
        $attributeLoggerMock = $this->createMock(Logger\AttributeLogger::class);

        if ($minimumLogLevel >= Logger::LOGLEVEL_ERROR) {
            if ($logToStdErr) {
                $eventLoggerMock
                    ->expects($this->at(0))
                    ->method('logToSystem')
                    ->with(LogSystemWriter::TARGET_STDERR);
            }
            if ($logToStdOut) {
                $eventLoggerMock
                    ->expects($this->at($logToStdErr ? 1 : 0))
                    ->method('logToSystem')
                    ->with(LogSystemWriter::TARGET_STDOUT);
            }
            if ($logToFile) {
                $eventLoggerMock
                    ->expects($this->atMost(2))
                    ->method('logToFile');
            }
        }
        if (
            $minimumLogLevel < Logger::LOGLEVEL_ERROR
            || (!$logToStdErr && !$logToStdOut && !$logToFile)
        ) {
            $eventLoggerMock
                ->expects($this->never())
                ->method('logToSystem');
            $eventLoggerMock
                ->expects($this->never())
                ->method('logToFile');
        }
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $systemConfigurationMock->expects($this->atMost(1))
            ->method('getLoxBerryVersion')
            ->willReturn('1.5.0');

        $logger = new Logger('test', 'test', $eventLoggerMock, $attributeLoggerMock, $systemConfigurationMock);
        $logger->setWriteToFile($logToFile);
        $logger->setWriteToStdErr($logToStdErr);
        $logger->setWriteToStdOut($logToStdOut);
        $logger->setMinimumLogLevel($minimumLogLevel);
        $logger->log('test', Logger::LOGLEVEL_ERROR);
    }

    /**
     * @dataProvider helperMethodsProvider
     */
    public function testHelperMethods($method, $level)
    {
        $logger = $this->createPartialMock(Logger::class, ['log']);
        $logger->expects($this->once())
            ->method('log')
            ->with('testmessage', $level);

        $logger->{$method}('testmessage');
    }

    public function testSeverityIsProperlyProtocolled()
    {
        $eventLoggerMock = $this->createMock(Logger\EventLogger::class);
        $attributeLoggerMock = $this->createMock(Logger\AttributeLogger::class);
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $systemConfigurationMock->expects($this->atMost(1))
            ->method('getLoxBerryVersion')
            ->willReturn('1.5.0');
        $logger = new Logger('test', 'test', $eventLoggerMock, $attributeLoggerMock, $systemConfigurationMock);
        $logger->setWriteToFile(false);
        $logger->setMinimumLogLevel(Logger::LOGLEVEL_OK);

        $this->assertCount(0, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_DEBUG, $logger->getMaximumSeverityEncountered());

        $logger->info('test');
        $this->assertCount(0, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_DEBUG, $logger->getMaximumSeverityEncountered());

        $logger->success('test');
        $this->assertCount(0, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_OK, $logger->getMaximumSeverityEncountered());

        $logger->error('test');
        $this->assertCount(1, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_ERROR, $logger->getMaximumSeverityEncountered());

        $logger->error('test');
        $this->assertCount(2, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_ERROR, $logger->getMaximumSeverityEncountered());

        $logger->critical('test');
        $this->assertCount(3, $logger->getSevereLogEvents());
        $this->assertEquals(Logger::LOGLEVEL_CRITICAL_ERROR, $logger->getMaximumSeverityEncountered());
    }

    public function testCanLogEvent()
    {
        $eventLoggerMock = $this->createMock(Logger\EventLogger::class);
        $attributeLoggerMock = $this->createMock(Logger\AttributeLogger::class);
        $logEvent = new LogEvent('test', Logger::LOGLEVEL_ERROR, 'testFile', 22);

        $eventLoggerMock->expects($this->once())
            ->method('logToFile')
            ->with($logEvent);

        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $systemConfigurationMock->expects($this->atMost(1))
            ->method('getLoxBerryVersion')
            ->willReturn('1.5.0');

        $logger = new Logger('test', 'test', $eventLoggerMock, $attributeLoggerMock, $systemConfigurationMock);
        $logger->setMinimumLogLevel(Logger::LOGLEVEL_OK);
        $logger->log($logEvent);
    }

    public function testMultipleEventsAreLoggedProperly()
    {
        $eventLoggerMock = $this->createMock(Logger\EventLogger::class);
        $attributeLoggerMock = $this->createMock(Logger\AttributeLogger::class);
        $logEvent = new LogEvent('test', Logger::LOGLEVEL_ERROR, 'testFile', 22);

        $eventLoggerMock->expects($this->exactly(2))
            ->method('logToFile')
            ->with($logEvent);

        $logEvents = [$logEvent, $logEvent];
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $systemConfigurationMock->expects($this->atMost(1))
            ->method('getLoxBerryVersion')
            ->willReturn('1.5.0');

        $logger = new Logger('test', 'test', $eventLoggerMock, $attributeLoggerMock, $systemConfigurationMock);
        $logger->setMinimumLogLevel(Logger::LOGLEVEL_OK);
        $logger->logEvents($logEvents);
    }

    public function logConfigurationTestDataProvider()
    {
        return [
            [1, 0, 0, 7],
            [0, 1, 0, 7],
            [0, 0, 1, 7],
            [0, 0, 0, 7],
            [1, 1, 0, 7],
            [1, 1, 1, 7],
            [1, 0, 0, 4],
            [0, 1, 0, 4],
            [0, 0, 1, 4],
            [0, 0, 0, 4],
            [1, 1, 0, 4],
            [1, 1, 1, 4],
            [1, 0, 0, 2],
            [0, 1, 0, 2],
            [0, 0, 1, 2],
            [0, 0, 0, 2],
            [1, 1, 0, 2],
            [1, 1, 1, 2],
        ];
    }

    public function helperMethodsProvider()
    {
        return [
          ['fatal', Logger::LOGLEVEL_FATAL_ERROR],
          ['critical', Logger::LOGLEVEL_CRITICAL_ERROR],
          ['alert', Logger::LOGLEVEL_ALERT],
          ['error', Logger::LOGLEVEL_ERROR],
          ['warn', Logger::LOGLEVEL_WARNING],
          ['success', Logger::LOGLEVEL_OK],
          ['info', Logger::LOGLEVEL_INFO],
          ['debug', Logger::LOGLEVEL_DEBUG],
        ];
    }
}
