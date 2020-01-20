<?php

namespace LoxBerry\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
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

    /** @var LogEvent[]|array */
    private $severeLogEvents = [];

    /** @var int */
    private $maximumSeverityEncountered = self::LOGLEVEL_DEBUG;

    /** @var SystemConfigurationParser */
    private $systemConfiguration;

    /** @var bool */
    private $started = false;

    /** @var int */
    private $logKey;

    /**
     * Logger constructor.
     *
     * @param string                    $logName
     * @param string                    $packageName
     * @param EventLogger               $eventLogger
     * @param AttributeLogger           $attributeLogger
     * @param SystemConfigurationParser $systemConfiguration
     */
    public function __construct(
        string $logName,
        string $packageName,
        EventLogger $eventLogger,
        AttributeLogger $attributeLogger,
        SystemConfigurationParser $systemConfiguration
    ) {
        $this->logName = $logName;
        $this->logPackage = $packageName;
        $this->eventLogger = $eventLogger;
        $this->attributeLogger = $attributeLogger;
        $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * @param string|LogEvent $message
     * @param int             $level
     */
    public function log($messageOrEvent, int $level = self::LOGLEVEL_DEBUG)
    {
        if (!$this->started) {
            $this->logStart();
        }

        $logEvent = $this->prepareLogEvent($messageOrEvent, $level);

        if ($logEvent->getLevel() > $this->minimumLogLevel) {
            return;
        }

        if ($logEvent->getLevel() <= self::LOGLEVEL_WARNING) {
            $this->severeLogEvents[] = $logEvent;
        }

        if ($logEvent->getLevel() < $this->maximumSeverityEncountered) {
            $this->maximumSeverityEncountered = $level;
            $this->setLogAttribute('STATUS', $this->maximumSeverityEncountered);
        }

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

    public function logStart(?string $logStartMessage = null)
    {
        if ($this->started) {
            return;
        }

        if ($this->writeToFile) {
            $this->logKey = $this->attributeLogger->getDatabase()->logStart(
                $this->logPackage,
                $this->logName,
                $this->eventLogger->getFileWriter()->getOutputFileName()
            );

            $this->eventLogger->getFileWriter()->logStart();

            $this->started = true;
            $this->info('LoxBerry Version '.$this->systemConfiguration->getLoxBerryVersion());

            $this->setLogAttribute('LOGSTARTMESSAGE', $logStartMessage ?? $this->logName);
            $this->setLogAttribute('STATUS', $this->maximumSeverityEncountered);
            $this->setLogAttribute('_ISPLUGIN', 1);
            $this->setLogAttribute('PLUGINTITLE', $this->logPackage);
            $this->setLogAttribute('PACKAGE', $this->logPackage);
            $this->setLogAttribute('NAME', $this->logName);

            register_shutdown_function([$this, 'logEnd']);
        }
    }

    /**
     * @param string|null $message
     *
     * @throws \Exception
     */
    public function logEnd(?string $message = 'Task finished')
    {
        if (!$this->started) {
            return;
        }

        if ($this->writeToFile) {
            $this->eventLogger->getFileWriter()->logEnd($message);
            $this->setLogAttribute('LOGENDMESSAGE', $message);
            $this->setLogAttribute('ATTENTIONMESSAGES', implode(PHP_EOL, array_map(function (LogEvent $logEvent) {
                return $logEvent->getMessage();
            }, $this->severeLogEvents ?? [])));
            foreach ($this->logAttributes as $key => $value) {
                $this->attributeLogger->logAttribute($this->logKey, $key, $value);
            }
        }

        $this->started = false;
    }

    /**
     * @param string $message
     */
    public function debug(string $message)
    {
        $this->log($message);
    }

    /**
     * @param string $message
     */
    public function info(string $message)
    {
        $this->log($message, self::LOGLEVEL_INFO);
    }

    /**
     * @param string $message
     */
    public function success(string $message)
    {
        $this->log($message, self::LOGLEVEL_OK);
    }

    /**
     * @param string $message
     */
    public function warn(string $message)
    {
        $this->log($message, self::LOGLEVEL_WARNING);
    }

    /**
     * @param string $message
     */
    public function error(string $message)
    {
        $this->log($message, self::LOGLEVEL_ERROR);
    }

    /**
     * @param string $message
     */
    public function alert(string $message)
    {
        $this->log($message, self::LOGLEVEL_ALERT);
    }

    /**
     * @param string $message
     */
    public function critical(string $message)
    {
        $this->log($message, self::LOGLEVEL_CRITICAL_ERROR);
    }

    /**
     * @param string $message
     */
    public function fatal(string $message)
    {
        $this->log($message, self::LOGLEVEL_FATAL_ERROR);
    }

    /**
     * @param LogEvent[]|array $logEvents
     */
    public function logEvents(array $logEvents)
    {
        foreach ($logEvents as $logEvent) {
            if (!$logEvent instanceof LogEvent) {
                throw new \InvalidArgumentException('Method can only handle objects of type LogEvent');
            }
        }

        array_map([$this, 'log'], $logEvents);
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

    /**
     * @return array|LogEvent[]
     */
    public function getSevereLogEvents()
    {
        return $this->severeLogEvents;
    }

    /**
     * @return int
     */
    public function getMaximumSeverityEncountered(): int
    {
        return $this->maximumSeverityEncountered;
    }

    /**
     * @return int
     */
    public function getLogKey(): int
    {
        return $this->logKey;
    }

    /**
     * @param $messageOrEvent
     * @param int $level
     *
     * @return LogEvent
     */
    private function prepareLogEvent($messageOrEvent, int $level): LogEvent
    {
        if ($messageOrEvent instanceof LogEvent) {
            $logEvent = $messageOrEvent;
        } elseif (is_string($messageOrEvent)) {
            $logEvent = new LogEvent($messageOrEvent, $level);
        }

        if (!isset($logEvent) || !$logEvent instanceof LogEvent) {
            throw new \InvalidArgumentException('Logger can only handle LogEvents or Strings');
        }

        return $logEvent;
    }
}
