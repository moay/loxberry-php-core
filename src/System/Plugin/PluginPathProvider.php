<?php

namespace LoxBerry\System\Plugin;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;

/**
 * Class PluginPathProvider.
 */
class PluginPathProvider
{
    const KNOWN_PATHS = [
        Paths::PATH_PLUGIN_HTMLAUTH,
        Paths::PATH_PLUGIN_HTML,
        Paths::PATH_PLUGIN_TEMPLATE,
        Paths::PATH_PLUGIN_DATA,
        Paths::PATH_PLUGIN_LOG,
        Paths::PATH_PLUGIN_CONFIG,
        Paths::PATH_PLUGIN_BIN,
    ];

    /** @var PathProvider */
    private $pathProvider;

    /** @var string */
    private $pluginName;

    /**
     * PluginPathProvider constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param string $pathName
     *
     * @return string
     */
    public function getPath(string $pathName)
    {
        if (!is_string($this->pluginName)) {
            throw new \LogicException('Cannot determine plugin path, plugin name is not set. Call setPluginName first.');
        }
        if (!in_array($pathName, self::KNOWN_PATHS)) {
            throw new \InvalidArgumentException(sprintf('Unknown plugin path %s requested', $pathName));
        }

        switch ($pathName) {
            case Paths::PATH_PLUGIN_HTMLAUTH:
                $basePath = Paths::PATH_SYSTEM_HTMLAUTH;
                break;
            case Paths::PATH_PLUGIN_HTML:
                $basePath = Paths::PATH_SYSTEM_HTML;
                break;
            case Paths::PATH_PLUGIN_TEMPLATE:
                $basePath = Paths::PATH_SYSTEM_TEMPLATE;
                break;
            case Paths::PATH_PLUGIN_DATA:
                $basePath = Paths::PATH_SYSTEM_DATA;
                break;
            case Paths::PATH_PLUGIN_LOG:
                $basePath = Paths::PATH_SYSTEM_LOG;
                break;
            case Paths::PATH_PLUGIN_CONFIG:
                $basePath = Paths::PATH_SYSTEM_CONFIG;
                break;
            case Paths::PATH_PLUGIN_BIN:
                $basePath = Paths::PATH_SYSTEM_BIN;
                break;
        }

        return $this->getPluginDirectoryFromSystem($basePath).DIRECTORY_SEPARATOR.$this->pluginName;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * @param string $pluginName
     */
    public function setPluginName(string $pluginName): void
    {
        $this->pluginName = $pluginName;
    }

    /**
     * @param $directoryName
     *
     * @return string
     */
    private function getPluginDirectoryFromSystem($basePath)
    {
        return $this->pathProvider->getPath($basePath).DIRECTORY_SEPARATOR.'plugins';
    }
}
