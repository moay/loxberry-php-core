<?php

namespace LoxBerry\Logging\Logger;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Writer\LogFileWriter;
use LoxBerry\Logging\Writer\LogSystemWriter;

/**
 * Class EventLogger.
 */
class EventLogger
{
    /** @var LogFileWriter */
    private $fileWriter;

    /** @var LogSystemWriter */
    private $systemWriter;

    /**
     * EventLogger constructor.
     *
     * @param LogFileWriter   $fileWriter
     * @param LogSystemWriter $systemWriter
     */
    public function __construct(LogFileWriter $fileWriter, LogSystemWriter $systemWriter)
    {
        $this->fileWriter = $fileWriter;
        $this->systemWriter = $systemWriter;
    }

    public function logToFile(LogEvent $event)
    {
        // Todo: Test & implement
    }

    public function logToSystem(LogEvent $event)
    {
        // Todo: Test & implement
    }
}
