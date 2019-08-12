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

    /** @var string */
    private $package;

    /** @var string */
    private $name;

    /**
     * AttributeLogger constructor.
     *
     * @param LogFileDatabase $database
     * @param string          $package
     * @param string          $name
     */
    public function __construct(LogFileDatabase $database, string $package, string $name)
    {
        $this->database = $database;
        $this->package = $package;
        $this->name = $name;
    }

    public function logAttribute(string $key, $value)
    {
        // Todo: Test & implement
    }
}
