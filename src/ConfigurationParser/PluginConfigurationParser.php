<?php

namespace LoxBerry\ConfigurationParser;

use LoxBerry\System\PathProvider;

/**
 * Class PluginConfigurationParser.
 */
class PluginConfigurationParser
{
    /** @var PathProvider */
    private $pathProvider;

    /** @var string */
    private $pluginName;

    /**
     * PluginConfigurationParser constructor.
     *
     * @param PathProvider $pathProvider
     * @param string       $pluginName
     */
    public function __construct(PathProvider $pathProvider, string $pluginName)
    {
        $this->pathProvider = $pathProvider;
        $this->pluginName = $pluginName;
    }
}
