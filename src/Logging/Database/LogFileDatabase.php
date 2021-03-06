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
            'LOGKEY' => ['INTEGER', 'NOT NULL', 'PRIMARY KEY'],
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
     * @param int $logKey
     *
     * @return array|null
     */
    public function getUnclosedLogSessionByKey(int $logKey): ?array
    {
        $sessions = $this->database->select(
            'logs',
            ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'],
            ['LOGKEY' => $logKey, 'LOGEND[!]' => null]
        );

        if (!is_array($sessions) || 0 === count($sessions)) {
            throw new LogFileDatabaseException('Cannot recreate logging session, provided log key was not found or session is closed');
        }

        return $sessions[0];
    }

    /**
     * @param string $packageName
     * @param string $logName
     * @param string $fileName
     *
     * @return int
     */
    public function logStart(string $packageName, string $logName, string $fileName, ?\DateTime $logStart = null): int
    {
        $logStart = ($logStart ?? new \DateTime())->format('Y-m-d H:i:s');

        $this->database->insert('logs', [
            'PACKAGE' => $packageName,
            'NAME' => $logName,
            'FILENAME' => $fileName,
            'LOGSTART' => $logStart,
            'LASTMODIFIED' => $logStart,
        ]);

        $logKey = $this->database->id();

        register_shutdown_function([$this, 'logEnd'], $logKey, true);

        return $logKey;
    }

    /**
     * @param int $logKey
     *
     * @throws \Exception
     */
    public function logEnd(int $logKey, bool $isShutdown = false)
    {
        $record = $this->database->select('logs', '*', ['LOGKEY' => $logKey])[0] ?? null;
        if (null === $record) {
            if (!$isShutdown) {
                throw new LogFileDatabaseException('Cannot find log session to close');
            }

            return;
        }

        $logEnd = (new \DateTime())->format('Y-m-d H:i:s');
        $this->database->update('logs', [
            'LOGEND' => $logEnd,
            'LASTMODIFIED' => $logEnd,
        ], ['LOGKEY' => $logKey]);
    }

    /**
     * @param int    $logKey
     * @param string $attributeKey
     * @param $value
     */
    public function logAttribute(int $logKey, string $attributeKey, $value)
    {
        $this->database->query(
            'INSERT OR REPLACE INTO <logs_attr> (<keyref>, <attrib>, <value>) VALUES (:keyref, :attrib, :value)',
            [
                ':keyref' => $logKey,
                ':attrib' => $attributeKey,
                ':value' => $value,
            ]
        );
    }

    /**
     * @param int    $logKey
     * @param string $attributeKey
     *
     * @return mixed|null
     */
    public function getAttribute(int $logKey, string $attributeKey)
    {
        $attributes = $this->database->select(
            'logs_attr',
            'value',
            [
                'keyref' => $logKey,
                'attrib' => $attributeKey,
            ]
        );

        return $attributes[0] ?? null;
    }

    /**
     * @param int $logKey
     *
     * @return array
     */
    public function getAllAttributes(int $logKey): array
    {
        $attributes = $this->database->select(
            'logs_attr',
            ['attrib', 'value'],
            ['keyref' => $logKey]
        );
        $mappedAttributes = [];

        foreach ($attributes as $attribute) {
            $mappedAttributes[$attribute['attrib']] = $attribute['value'];
        }

        return $mappedAttributes;
    }
}
