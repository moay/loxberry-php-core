<?php

namespace LoxBerry\Logging;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use Medoo\Medoo;

/**
 * Class LogFileDatabaseFactory.
 */
class LogFileDatabaseFactory
{
    /** @var PathProvider */
    private $pathProvider;

    /**
     * LogFileDatabaseFactory constructor.
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @return LogFileDatabase
     */
    public function create(): LogFileDatabase
    {
        $database = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => rtrim($this->pathProvider->getPath(Paths::PATH_LOG_DATABASE_FILE)),
        ]);

        return new LogFileDatabase($database);
    }
}
