<?php

namespace LoxBerry\Logging\Writer;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\PathProvider;

/**
 * Class LogFileInitializer.
 */
class LogFileInitializer
{
    /** @var PathProvider */
    private $pathProvider;

    /** @var SystemConfigurationParser */
    private $systemConfiguration;

    /**
     * LogFileInitializer constructor.
     *
     * @param PathProvider              $pathProvider
     * @param SystemConfigurationParser $systemConfiguration
     */
    public function __construct(PathProvider $pathProvider, SystemConfigurationParser $systemConfiguration)
    {
        $this->pathProvider = $pathProvider;
        $this->systemConfiguration = $systemConfiguration;
    }

    public function initialize(string $directory, string $fileName, bool $removeExisting = true)
    {
        // Todo: test & initialize - create file if needed, remove old if needed, register shutdown for cleanup
    }

    public function close()
    {
        // Todo: test & implement, write file end
    }
}
