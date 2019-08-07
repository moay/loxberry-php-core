<?php

namespace LoxBerry\Logging;

use Medoo\Medoo;

/**
 * Class LogFileDatabase.
 */
class LogFileDatabase
{
    /** @var Medoo */
    private $database;

    /**
     * LogFileDatabase constructor.
     * @param Medoo $database
     */
    public function __construct(Medoo $database)
    {
        $this->database = $database;
        $this->initializeDatabase();
    }

    private function initializeDatabase()
    {
        $this->database->query('PRAGMA journal_mode = wal;');
    }
}
