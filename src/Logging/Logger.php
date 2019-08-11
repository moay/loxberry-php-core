<?php

namespace LoxBerry\Logging;

use LoxBerry\Logging\Database\LogFileDatabase;
use LoxBerry\Logging\Writer\LogFileWriter;
use LoxBerry\Logging\Writer\LogSystemWriter;

/**
 * Class Logger.
 */
class Logger
{
    const LOGLEVEL_FATAL_ERROR = 0;
    const LOGLEVEL_ALERT = 1;
    const LOGLEVEL_CRITICAL_ERROR = 2;
    const LOGLEVEL_ERROR = 3;
    const LOGLEVEL_WARNING = 4;
    const LOGLEVEL_OK = 5;
    const LOGLEVEL_INFO = 6;
    const LOGLEVEL_DEBUG = 7;

    const KNOWN_LOGLEVELS = [
        self::LOGLEVEL_FATAL_ERROR,
        self::LOGLEVEL_ALERT,
        self::LOGLEVEL_CRITICAL_ERROR,
        self::LOGLEVEL_ERROR,
        self::LOGLEVEL_WARNING,
        self::LOGLEVEL_OK,
        self::LOGLEVEL_INFO,
        self::LOGLEVEL_DEBUG,
    ];

    /** @var string */
    private $logName;

    /** @var string */
    private $logPackage;

    /** @var bool */
    private $writeToStdErr = false;

    /** @var bool */
    private $writeToStdOut = false;

    /** @var bool */
    private $writeToFile = true;

    /** @var bool */
    private $deletePreviousLogFiles = true;

    /** @var LogFileDatabase|null */
    private $database;

    /** @var LogFileWriter|null */
    private $fileWriter;

    /** @var LogSystemWriter|null */
    private $systemWriter;

    /**
     * Logger constructor.
     *
     * @param string $logName
     * @param string $packageName
     */
    public function __construct(string $logName, string $packageName)
    {
        $this->logName = $logName;
        $this->logPackage = $packageName;
    }

    public function log(string $message, int $level = self::LOGLEVEL_DEBUG)
    {
        // Todo: test & implement
    }

    public function debug(string $message)
    {
        // Todo: test & implement
    }

    public function info(string $message)
    {
        // Todo: test & implement
    }

    public function success(string $message)
    {
        // Todo: test & implement
    }

    public function warn(string $message)
    {
        // Todo: test & implement
    }

    public function error(string $message)
    {
        // Todo: test & implement
    }

    public function alert(string $message)
    {
        // Todo: test & implement
    }

    public function fatal(string $message)
    {
        // Todo: test & implement
    }

    /**
     * @return string
     */
    public function getLogName(): string
    {
        return $this->logName;
    }

    /**
     * @return string
     */
    public function getLogPackage(): string
    {
        return $this->logPackage;
    }

    /**
     * @param bool $writeToStdErr
     */
    public function setWriteToStdErr(bool $writeToStdErr): void
    {
        $this->writeToStdErr = $writeToStdErr;
    }

    /**
     * @param bool $writeToStdOut
     */
    public function setWriteToStdOut(bool $writeToStdOut): void
    {
        $this->writeToStdOut = $writeToStdOut;
    }

    /**
     * @param bool $writeToFile
     */
    public function setWriteToFile(bool $writeToFile): void
    {
        $this->writeToFile = $writeToFile;
    }

    /**
     * @param bool $deletePreviousLogFiles
     */
    public function setDeletePreviousLogFiles(bool $deletePreviousLogFiles): void
    {
        $this->deletePreviousLogFiles = $deletePreviousLogFiles;
    }

    /**
     * @param LogFileDatabase $database
     */
    public function setDatabase(LogFileDatabase $database): void
    {
        $this->database = $database;
    }

    /**
     * @param LogFileWriter $fileWriter
     */
    public function setFileWriter(LogFileWriter $fileWriter): void
    {
        $this->fileWriter = $fileWriter;
    }

    /**
     * @param LogSystemWriter $systemWriter
     */
    public function setSystemWriter(LogSystemWriter $systemWriter): void
    {
        $this->systemWriter = $systemWriter;
    }
}
