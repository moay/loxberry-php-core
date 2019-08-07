<?php

namespace LoxBerry\Logging;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use LoxBerry\Utility\LowLevel;
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
    public function __construct(PathProvider $pathProvider, LowLevel $lowLevel)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @return LogFileDatabase
     */
    public function create(bool $forceRecreate = false): LogFileDatabase
    {
        $databaseFilePath = $this->pathProvider->getPath(Paths::PATH_LOG_DATABASE_FILE);

        if ($forceRecreate && file_exists($databaseFilePath)) {
            unlink($databaseFilePath);
        }

        if (LowLevel::USERNAME !== LowLevel::getFileOwner($databaseFilePath)) {
            LowLevel::changeFileOwner($databaseFilePath, LowLevel::USERNAME);
        }

        $database = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => $this->pathProvider->getPath(Paths::PATH_LOG_DATABASE_FILE),
        ]);

        return new LogFileDatabase($database);
    }
}
