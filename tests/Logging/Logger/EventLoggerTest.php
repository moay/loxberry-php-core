<?php

namespace LoxBerry\Tests\Logging\Logger;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\Writer\LogFileWriter;
use LoxBerry\Logging\Writer\LogSystemWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class EventLoggerTest.
 */
class EventLoggerTest extends TestCase
{
    public function testEventGetsLoggedToFileProperly()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $fileWriterMock = $this->createMock(LogFileWriter::class);
        $fileWriterMock->expects($this->once())
            ->method('logEvent')
            ->with($logEvent);

        $eventLogger = new Logger\EventLogger();
        $eventLogger->setFileWriter($fileWriterMock);
        $eventLogger->logToFile($logEvent);
    }

    public function testEventLoggerThrowsExceptionIfNoFileWriterProvided()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $this->expectException(\RuntimeException::class);

        $eventLogger = new Logger\EventLogger();
        $eventLogger->logToFile($logEvent);
    }

    public function testEventGetsLoggedToSystemProperly()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $systemWriterMock = $this->createMock(LogSystemWriter::class);
        $systemWriterMock->expects($this->once())
            ->method('logEventTo')
            ->with(0, $logEvent);

        $eventLogger = new Logger\EventLogger();
        $eventLogger->setSystemWriter($systemWriterMock);
        $eventLogger->logToSystem(LogSystemWriter::TARGET_STDOUT, $logEvent);
    }

    public function testEventLoggerThrowsExceptionIfNoSystemWriterProvided()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $this->expectException(\RuntimeException::class);

        $eventLogger = new Logger\EventLogger();
        $eventLogger->logToSystem(LogSystemWriter::TARGET_STDOUT, $logEvent);
    }

    public function testEventLoggerThrowsExceptionWhenUsingInvalidTarget()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $this->expectException(\RuntimeException::class);

        $eventLogger = new Logger\EventLogger();
        $eventLogger->logToSystem(12, $logEvent);
    }
}
