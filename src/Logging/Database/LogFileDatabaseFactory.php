<?php

namespace LoxBerry\Logging\Database;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use LoxBerry\System\LowLevelExecutor;
use Medoo\Medoo;

/**
 * Class LogFileDatabaseFactory.
 */
class LogFileDatabaseFactory
{
    /** @var PathProvider */
    private $pathProvider;

    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * LogFileDatabaseFactory constructor.
     *
     * @param PathProvider     $pathProvider
     * @param LowLevelExecutor $lowLevel
     */
    public function __construct(PathProvider $pathProvider, LowLevelExecutor $lowLevel)
    {
        $this->pathProvider = $pathProvider;
        $this->lowLevel = $lowLevel;
    }

    /**
     * @param bool $forceRecreate
     *
     * @return LogFileDatabase
     */
    public function create(bool $forceRecreate = false): LogFileDatabase
    {
        $databaseFilePath = $this->pathProvider->getPath(Paths::PATH_LOG_DATABASE_FILE);

        if ($forceRecreate && file_exists($databaseFilePath)) {
            unlink($databaseFilePath);
        }

        if (LowLevelExecutor::USERNAME !== $this->lowLevel->getFileOwner($databaseFilePath)) {
            $this->lowLevel->setFileOwner($databaseFilePath, LowLevelExecutor::USERNAME);
        }

        $database = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => $databaseFilePath,
        ]);

        return new LogFileDatabase($database);
    }
}
