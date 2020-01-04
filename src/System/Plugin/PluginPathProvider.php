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

        return $this->getPluginDirectoryFromSystem($pathName).DIRECTORY_SEPARATOR.$this->pluginName;
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
     * @param $path
     *
     * @return string
     */
    private function getPluginDirectoryFromSystem($path)
    {
        return $this->pathProvider->getPath($path);
    }
}
