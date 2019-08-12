<?php

namespace LoxBerry\Logging\Database;

use LoxBerry\Exceptions\LogFileDatabaseException;
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
     *
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
            'LASTMODIFIED' => ['DATETIME', 'NOT NULL'],
        ]);
        $this->database->create('logs_attr', [
            'keyref' => ['INT', 'NOT NULL'],
            'attrib' => ['VARCHAR(255)', 'NOT NULL'],
            'value' => ['VARCHAR(255)'],
            'PRIMARY KEY (<keyref>, <attrib>)',
        ]);
    }

    /**
     * @param string $logKey
     *
     * @return array|null
     */
    public function getUnclosedLogSessionByKey(string $logKey): ?array
    {
        $sessions = $this->database->select(
            'PACKAGE',
            ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'],
            ['LOGKEY' => $logKey, 'LOGEND[!]' => null]
        );

        if (!is_array($sessions) || 0 === count($sessions)) {
            throw new LogFileDatabaseException('Cannot recreate logging session, provided log key was not found or session is closed');
        }

        return $sessions[0];
    }

    public function logStart()
    {
        // Todo: Test & Implement start of logging, register shutdown to logEnd
    }

    public function logEnd()
    {
        // Todo: Test & Implement end of logging (with attributes ?)
    }

    /**
     * @param string $logKey
     * @param string $attributeKey
     * @param $value
     */
    public function logAttribute(string $logKey, string $attributeKey, $value)
    {
        // Todo: Test & implement
    }

    public function getAttribute(string $logKey, string $attributeKey)
    {
        // Todo: Test & implement
    }

    public function getAllAttributes(string $logKey)
    {
        // Todo: Test & implement
    }
}
