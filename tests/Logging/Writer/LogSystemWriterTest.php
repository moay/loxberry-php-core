<?php

namespace LoxBerry\Tests\Logging\Writer;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\Writer\LogSystemWriter;
use LoxBerry\Utility\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class LogSystemWriterTest.
 */
class LogSystemWriterTest extends TestCase
{
    public function testAllowsOnlyDefinedTargets()
    {
        $lowLevelExecutorMock = $this->createMock(LowLevelExecutor::class);
        $logEventMock = $this->createMock(LogEvent::class);
        $logSystemWriter = new LogSystemWriter($lowLevelExecutorMock);

        $this->expectException(\InvalidArgumentException::class);
        $logSystemWriter->writeTo(3, $logEventMock);
    }

    /**
     * @dataProvider logEventProvider
     *
     * @param $target
     * @param LogEvent $event
     *
     * @throws \Exception
     */
    public function testWritesToTargetViaLowLevel($target, $event)
    {
        $lowLevelExecutorMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelExecutorMock
            ->expects($this->once())
            ->method('fwrite')
            ->with(LogSystemWriter::TARGET_STDERR === $target ? STDERR : STDOUT, 'testerror (testfile, 22)'.PHP_EOL)
            ->willReturn('test');

        $logSystemWriter = new LogSystemWriter($lowLevelExecutorMock);

        $logSystemWriter->writeTo($target, $event);
    }

    public function logEventProvider()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        return [
            [LogSystemWriter::TARGET_STDOUT, $logEvent],
            [LogSystemWriter::TARGET_STDERR, $logEvent],
        ];
    }
}
