<?php

namespace LoxBerry\Logging\Writer;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Utility\LowLevelExecutor;

/**
 * Class LogSystemWriter.
 */
class LogSystemWriter
{
    const TARGET_STDOUT = 0;
    const TARGET_STDERR = 1;

    const KNOWN_TARGETS = [
        self::TARGET_STDOUT,
        self::TARGET_STDERR,
    ];

    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * LogSystemWriter constructor.
     *
     * @param LowLevelExecutor $lowLevel
     */
    public function __construct(LowLevelExecutor $lowLevel)
    {
        $this->lowLevel = $lowLevel;
    }

    /**
     * @param int      $target
     * @param LogEvent $event
     */
    public function writeTo(int $target, LogEvent $event)
    {
        if (!in_array($target, self::KNOWN_TARGETS)) {
            throw new \InvalidArgumentException('Cannot write to provided target.');
        }

        $this->lowLevel->fwrite(self::TARGET_STDOUT === $target ? STDOUT : STDERR, sprintf(
            '%s (%s, %s)',
            $event->getMessage(),
            $event->getFileName(),
            $event->getLineNumber() ?? '?'
        ).PHP_EOL);
    }
}
