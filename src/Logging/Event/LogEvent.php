<?php

namespace LoxBerry\Logging\Event;

/**
 * Class LogEntry.
 */
class LogEvent
{
    /** @var string */
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
     *
     * @throws \Exception
     */
    public function __construct(string $message, int $level, string $fileName, ?string $lineNumber = null)
    {
        $this->message = $message;
        // Todo: Check if level is allowed
        $this->level = $level;
        $this->fileName = $fileName;
        $this->lineNumber = $lineNumber;
        $this->eventTime = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int|null
     */
    public function getLineNumber(): ?int
    {
        return $this->lineNumber;
    }

    /**
     * @param int|null $lineNumber
     */
    public function setLineNumber(?int $lineNumber): void
    {
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEventTime(): \DateTimeImmutable
    {
        return $this->eventTime;
    }

    /**
     * @param \DateTimeImmutable $eventTime
     */
    public function setEventTime(\DateTimeImmutable $eventTime): void
    {
        $this->eventTime = $eventTime;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}
