<?php

namespace LoxBerry\Logging;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\Logging\Database\LogFileDatabaseFactory;
use LoxBerry\Logging\Logger\AttributeLogger;
use LoxBerry\Logging\Logger\EventLogger;
use LoxBerry\Logging\Writer\LogFileWriter;
use LoxBerry\Logging\Writer\LogSystemWriter;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Plugin\PluginDatabase;

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

    /** @var PluginDatabase */
    private $pluginDatabase;

    /**
     * LoggerFactory constructor.
     *
     * @param LogFileDatabaseFactory    $databaseFactory
     * @param LowLevelExecutor          $lowLevelExecutor
     * @param PathProvider              $pathProvider
     * @param SystemConfigurationParser $systemConfiguration
     * @param PluginDatabase            $pluginDatabase
     */
    public function __construct(
        LogFileDatabaseFactory $databaseFactory,
        LowLevelExecutor $lowLevelExecutor,
        PathProvider $pathProvider,
        SystemConfigurationParser $systemConfiguration,
        PluginDatabase $pluginDatabase
    ) {
        $this->databaseFactory = $databaseFactory;
        $this->pathProvider = $pathProvider;
        $this->lowLevelExecutor = $lowLevelExecutor;
        $this->systemConfiguration = $systemConfiguration;
        $this->database = $this->databaseFactory->create();
        $this->pluginDatabase = $pluginDatabase;
    }

    /**
     * @param string      $logName
     * @param string      $packageName
     * @param string|null $fileName
     * @param bool        $writeToFile
     * @param bool        $writeToStdErr
     * @param bool        $writeToStdOut
     *
     * @return Logger
     */
    public function __invoke(
        string $logName,
        string $packageName,
        ?string $fileName = null,
        bool $writeToFile = true,
        bool $writeToStdErr = false,
        bool $writeToStdOut = false
    ) {
        $logger = $this->create($logName, $packageName, $fileName, $writeToFile, $writeToStdErr, $writeToStdOut);
        $pluginInformation = $this->pluginDatabase->getPluginInformation($packageName);
        $logger->setMinimumLogLevel($pluginInformation->getLogLevel() ?? Logger::LOGLEVEL_ERROR);

        return $logger;
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
        $eventLogger = new EventLogger();
        if ($writeToFile) {
            if (!is_string($fileName)) {
                throw new \InvalidArgumentException('Cannot enable file writing without logFile');
            }
            $eventLogger->setFileWriter(new LogFileWriter($fileName));
        }
        if ($writeToStdOut || $writeToStdErr) {
            $eventLogger->setSystemWriter(new LogSystemWriter($this->lowLevelExecutor));
        }

        $logger = new Logger(
            $logName,
            $packageName,
            $eventLogger,
            new AttributeLogger($this->database),
            $this->systemConfiguration
        );

        $logger->setWriteToStdErr($writeToStdErr);
        $logger->setWriteToStdOut($writeToStdOut);
        $logger->setWriteToFile($writeToFile);

        return $logger;
    }

    /**
     * @param int $logKey
     *
     * @return Logger
     */
    public function createFromExistingLogSession(int $logKey): Logger
    {
        $session = $this->database->getUnclosedLogSessionByKey($logKey);
        if (null !== $session) {
            $stderr = $this->database->getAttribute($logKey, 'stderr') ?? false;
            $stdout = $this->database->getAttribute($logKey, 'stdout') ?? false;

            return $this->create($session['NAME'], $session['PACKAGE'], $session['FILENAME'], true, $stderr, $stdout);
        }
    }
}
