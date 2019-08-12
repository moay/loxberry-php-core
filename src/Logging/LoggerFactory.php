<?php

namespace LoxBerry\Logging;

use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\Logging\Writer\LogSystemWriter;
use LoxBerry\System\PathProvider;

/**
 * Class LoggerFactory.
 */
class LoggerFactory
{
    /** @var LogFileDatabaseFactory */
    private $databaseFactory;

    /** @var LogSystemWriter */
    private $systemWriter;

    /** @var PathProvider */
    private $pathProvider;

    /**
     * LoggerFactory constructor.
     *
     * @param LogFileDatabaseFactory $databaseFactory
     * @param LogSystemWriter        $systemWriter
     * @param PathProvider           $pathProvider
     */
    public function __construct(
        LogFileDatabaseFactory $databaseFactory,
        LogSystemWriter $systemWriter,
        PathProvider $pathProvider
    ) {
        $this->databaseFactory = $databaseFactory;
        $this->systemWriter = $systemWriter;
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param string $packageName
     * @param bool   $writeToFile
     * @param bool   $writeToStdErr
     * @param bool   $writeToStdOut
     *
     * @return Logger
     */
    public function create(
        string $logName,
        string $packageName,
        bool $writeToFile = true,
        bool $writeToStdErr = false,
        bool $writeToStdOut = false
    ): Logger {
        // Writing to file and database only if writeToFile. If so, logstart and logend to database, rest to file only

        // Todo: Test, Initialize Logger with database, if needed initialize with fileInitializer, pass filewriter and system writer
    }

    /**
     * @param string $logKey
     *
     * @return Logger
     */
    public function createFromExistingLogSession(string $logKey): Logger
    {
        // Todo: Test & implement, should return self::create from existing session if exists.
    }
}
