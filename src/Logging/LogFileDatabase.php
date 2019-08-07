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
        $this->database->create('logs', [
            'LOGKEY' => ['INT', 'NOT NULL', 'PRIMARY KEY'],
            'PACKAGE' => ['VARCHAR(255)', 'NOT NULL'],
            'NAME' => ['VARCHAR(255)', 'NOT NULL'],
            'FILENAME' => ['VARCHAR(255)', 'NOT NULL'],
            'LOGSTART' => ['DATETIME'],
            'LOGEND' => ['DATETIME'],
            'LASTMODIFIED' => ['DATETIME', 'NOT NULL']
        ]);
        $this->database->create('logs_attr', [
            'keyref' => ['INT', 'NOT NULL'],
            'attrib' => ['VARCHAR(255)', 'NOT NULL'],
            'value' => ['VARCHAR(255)'],
            'PRIMARY KEY (<keyref>, <attrib>)'
        ]);
    }
}
