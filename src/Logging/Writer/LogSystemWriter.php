<?php

namespace LoxBerry\Logging\Writer;

use LoxBerry\Logging\Event\LogEvent;

/**
 * Class LogSystemWriter.
 */
class LogSystemWriter
{
    const TARGET_STDOUT = 0;
    const TARGET_STDERR = 1;

    public function writeTo(int $target, LogEvent $event)
    {
        // Todo: Test & implement
    }
}
