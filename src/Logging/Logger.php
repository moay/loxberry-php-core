<?php

namespace LoxBerry\Logging;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Logger\AttributeLogger;
use LoxBerry\Logging\Logger\EventLogger;
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

    /** @var array */
    private $logAttributes = [];

    /** @var EventLogger */
    private $eventLogger;

    /** @var AttributeLogger */
    private $attributeLogger;

    /** @var int */
    private $minimumLogLevel = self::LOGLEVEL_DEBUG;

    /**
     * Logger constructor.
     *
     * @param string          $logName
     * @param string          $packageName
     * @param EventLogger     $eventLogger
     * @param AttributeLogger $attributeLogger
     */
    public function __construct(
        string $logName,
        string $packageName,
        EventLogger $eventLogger,
        AttributeLogger $attributeLogger
    ) {
        $this->logName = $logName;
        $this->logPackage = $packageName;
        $this->eventLogger = $eventLogger;
        $this->attributeLogger = $attributeLogger;
    }

    /**
     * @param string $message
     * @param int    $level
     *
     * @throws \Exception
     */
    public function log(string $message, int $level = self::LOGLEVEL_DEBUG)
    {
        if ($level > $this->minimumLogLevel) {
            return;
        }

        $logEvent = new LogEvent($message, $level);

        if ($this->writeToStdErr) {
            $this->eventLogger->logToSystem(LogSystemWriter::TARGET_STDERR, $logEvent);
        }
        if ($this->writeToStdOut) {
            $this->eventLogger->logToSystem(LogSystemWriter::TARGET_STDOUT, $logEvent);
        }
        if ($this->writeToFile) {
            $this->eventLogger->logToFile($logEvent);
        }
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function debug(string $message)
    {
        $this->log($message, self::LOGLEVEL_DEBUG);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function info(string $message)
    {
        $this->log($message, self::LOGLEVEL_INFO);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function success(string $message)
    {
        $this->log($message, self::LOGLEVEL_OK);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function warn(string $message)
    {
        $this->log($message, self::LOGLEVEL_WARNING);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function error(string $message)
    {
        $this->log($message, self::LOGLEVEL_ERROR);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function alert(string $message)
    {
        $this->log($message, self::LOGLEVEL_ALERT);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function critical(string $message)
    {
        $this->log($message, self::LOGLEVEL_CRITICAL_ERROR);
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function fatal(string $message)
    {
        $this->log($message, self::LOGLEVEL_FATAL_ERROR);
    }

    public function start()
    {
        // Todo: test & implement
    }

    public function end()
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
     * @param string $key
     * @param $value
     */
    public function setLogAttribute(string $key, $value)
    {
        $this->logAttributes[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getLogAttribute(string $key)
    {
        return $this->logAttributes[$key] ?? null;
    }

    /**
     * @return int
     */
    public function getMinimumLogLevel(): int
    {
        return $this->minimumLogLevel;
    }

    /**
     * @param int $minimumLogLevel
     */
    public function setMinimumLogLevel(int $minimumLogLevel): void
    {
        if (!in_array($minimumLogLevel, self::KNOWN_LOGLEVELS)) {
            throw new \InvalidArgumentException('Unknown loglevel');
        }

        $this->minimumLogLevel = $minimumLogLevel;
    }
}
