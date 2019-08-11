<?php

namespace LoxBerry\Logging\Event;

use LoxBerry\Logging\Logger;

/**
 * Class ExceptionLogEvent.
 */
class ExceptionLogEvent extends LogEvent
{
    /**
     * ExceptionLogEvent constructor.
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct(
            $exception->getMessage(),
            Logger::LOGLEVEL_ERROR,
            $exception->getFile(),
            $exception->getLine()
        );
    }
}
