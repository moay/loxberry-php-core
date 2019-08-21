<?php

namespace LoxBerry\Logging\Event;

/**
 * Class LogEntry.
 */
class LogEvent
{
    /** @var string|null */
    private $fileName;

    /** @var int|null */
    private $lineNumber;

    /** @var string */
    private $message;

    /** @var \DateTimeImmutable */
    private $eventTime;

    /** @var int */
    private $level;

    /**
     * LogEvent constructor.
     *
     * @param string      $message
     * @param int         $level
     * @param string      $fileName
     * @param string|null $lineNumber
     */
    public function __construct(string $message, int $level, ?string $fileName = null, ?string $lineNumber = null)
    {
        $this->message = $message;
        $this->level = $level;
        $this->fileName = $fileName;
        $this->lineNumber = $lineNumber;

        [$timestamp, $microseconds] = explode('.', microtime(true));
        $this->eventTime = new \DateTimeImmutable(date('Y-m-d H:i:s.', $timestamp).$microseconds);
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @return int|null
     */
    public function getLineNumber(): ?int
    {
        return $this->lineNumber;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEventTime(): \DateTimeImmutable
    {
        return $this->eventTime;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
