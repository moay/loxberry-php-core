<?php

namespace LoxBerry\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\System\PathProvider;
use LoxBerry\System\LowLevelExecutor;

/**
 * Class LoggerFactory.
 */
class LoggerFactory
{
    /** @var LogFileDatabaseFactory */
    private $databaseFactory;

    /** @var PathProvider */
    private $pathProvider;

    /** @var LowLevelExecutor */
    private $lowLevelExecutor;

    /** @var SystemConfigurationParser */
    private $systemConfiguration;

    /**
     * LoggerFactory constructor.
     *
     * @param LogFileDatabaseFactory    $databaseFactory
     * @param LowLevelExecutor          $lowLevelExecutor
     * @param PathProvider              $pathProvider
     * @param SystemConfigurationParser $systemConfiguration
     */
    public function __construct(
        LogFileDatabaseFactory $databaseFactory,
        LowLevelExecutor $lowLevelExecutor,
        PathProvider $pathProvider,
        SystemConfigurationParser $systemConfiguration
    ) {
        $this->databaseFactory = $databaseFactory;
        $this->pathProvider = $pathProvider;
        $this->lowLevelExecutor = $lowLevelExecutor;
        $this->systemConfiguration = $systemConfiguration;
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
        ?string $fileName = null,
        bool $writeToFile = true,
        bool $writeToStdErr = false,
        bool $writeToStdOut = false
    ): Logger {
        // Todo: Test, Initialize Logger with database, if needed initialize with fileInitializer, pass filewriter and system writer
    }

    /**
     * @param string $logKey
     *
     * @return Logger
     */
    public function createFromExistingLogSession(string $logKey): Logger
    {
        // Todo: Test & implement, should also set all params from existing session if exists.
    }
}
