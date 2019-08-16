<?php

namespace LoxBerry\Logging\Logger;

use LoxBerry\Logging\Database\LogFileDatabase;

/**
 * Class AttributeLogger.
 */
class AttributeLogger
{
    /** @var LogFileDatabase */
    private $database;

    /**
     * AttributeLogger constructor.
     *
     * @param LogFileDatabase $database
     */
    public function __construct(LogFileDatabase $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $logKey
     * @param string $key
     * @param $value
     */
    public function logAttribute(string $logKey, string $key, $value)
    {
        $this->database->logAttribute($logKey, $key, $value);
    }
}
