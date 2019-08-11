<?php

namespace LoxBerry\Logging\Writer;

use LoxBerry\Logging\Event\LogEvent;

/**
 * Class LogFileWriter.
 */
class LogFileWriter
{
    /** @var string */
    private $directory;

    /** @var string */
    private $fileName;

    /** @var LogFileInitializer */
    private $initializer;

    /** @var bool */
    private $initialized = false;

    /**
     * LogFileWriter constructor.
     *
     * @param string             $directory
     * @param string             $fileName
     * @param LogFileInitializer $initializer
     */
    public function __construct(string $directory, string $fileName, LogFileInitializer $initializer)
    {
        $this->directory = $directory;
        $this->fileName = $fileName;
        $this->initializer = $initializer;
    }

    public function write(LogEvent $event)
    {
        // Todo: Test & implement, initialize if needed
    }
}
