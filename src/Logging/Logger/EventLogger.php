<?php

namespace LoxBerry\Logging\Logger;

use LoxBerry\Exceptions\LogWriterException;
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
     * @param LogEvent $event
     */
    public function logToFile(LogEvent $event)
    {
        if (!$this->fileWriter instanceof LogFileWriter) {
            throw new LogWriterException('Cannot write to file without file writer');
        }

        $this->fileWriter->logEvent($event);
    }

    /**
     * @param int      $target
     * @param LogEvent $event
     */
    public function logToSystem(int $target, LogEvent $event)
    {
        if (!$this->systemWriter instanceof LogSystemWriter) {
            throw new LogWriterException('Cannot write to system without system writer');
        }

        if (!in_array($target, LogSystemWriter::KNOWN_TARGETS)) {
            throw new LogWriterException('Cannot write to unkown system target');
        }

        $this->systemWriter->logEventTo($target, $event);
    }

    /**
     * @return LogFileWriter
     */
    public function getFileWriter(): LogFileWriter
    {
        return $this->fileWriter;
    }

    /**
     * @param LogFileWriter $fileWriter
     */
    public function setFileWriter(LogFileWriter $fileWriter): void
    {
        $this->fileWriter = $fileWriter;
    }

    /**
     * @return LogSystemWriter
     */
    public function getSystemWriter(): LogSystemWriter
    {
        return $this->systemWriter;
    }

    /**
     * @param LogSystemWriter $systemWriter
     */
    public function setSystemWriter(LogSystemWriter $systemWriter): void
    {
        $this->systemWriter = $systemWriter;
    }
}
