<?php

namespace LoxBerry\System\Plugin;

use LoxBerry\Exceptions\PluginDatabaseException;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;

/**
 * Class PluginDatabase.
 */
class PluginDatabase
{
    const DATABASE_DELIMITER = '|';

    const FIELD_INDEX_MD5_CHECKSUM = 0;
    const FIELD_INDEX_AUTHOR_NAME = 1;
    const FIELD_INDEX_AUTHOR_EMAIL = 2;
    const FIELD_INDEX_VERSION = 3;
    const FIELD_INDEX_NAME = 4;
    const FIELD_INDEX_FOLDER = 5;
    const FIELD_INDEX_TITLE = 6;
    const FIELD_INDEX_UI_VERSION = 7;
    const FIELD_INDEX_AUTOUPDATE = 8;
    const FIELD_INDEX_RELEASECFG = 9;
    const FIELD_INDEX_PRERELEASECFG = 10;
    const FIELD_INDEX_LOGLEVEL = 11;

    /** @var PathProvider */
    private $pathProvider;

    /** @var PluginInformation[]|array */
    private $plugins;

    /**
     * PluginDatabase constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param string $pluginName
     *
     * @return PluginInformation
     */
    public function getPluginInformation(string $pluginName): PluginInformation
    {
        if (null === $this->plugins) {
            $this->loadDatabase();
        }

        foreach ($this->plugins as $pluginInformation) {
            if ($pluginInformation->getName() === $pluginName) {
                return $pluginInformation;
            }
        }

        throw new PluginDatabaseException(sprintf(
            'Plugin "%s" is not installed',
            $pluginName
        ));
    }

    /**
     * @return array
     */
    public function getAllPlugins(): array
    {
        if (null === $this->plugins) {
            $this->loadDatabase();
        }

        return $this->plugins;
    }

    /**
     * @param string $pluginName
     *
     * @return bool
     */
    public function isInstalledPlugin(string $pluginName): bool
    {
        if (null === $this->plugins) {
            $this->loadDatabase();
        }

        foreach ($this->plugins as $pluginInformation) {
            if ($pluginInformation->getName() === $pluginName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTimeOfLastDatabaseChange(): int
    {
        $databaseFileName = $this->pathProvider->getPath(Paths::PATH_PLUGIN_DATABASE_FILE);
        if (!is_file($databaseFileName) || !is_readable($databaseFileName)) {
            throw new PluginDatabaseException('Cannot open plugin database.');
        }

        $lastChange = filemtime($databaseFileName);
        if (time() - $lastChange > 60) {
            clearstatcache($databaseFileName);
            $lastChange = filemtime($databaseFileName);
        }

        return $lastChange;
    }

    private function loadDatabase()
    {
        $databaseFileName = $this->pathProvider->getPath(Paths::PATH_PLUGIN_DATABASE_FILE);
        if (!is_file($databaseFileName) || !is_readable($databaseFileName)) {
            throw new PluginDatabaseException('Cannot open plugin database.');
        }

        $databaseContentLines = file($databaseFileName, FILE_IGNORE_NEW_LINES);
        $this->plugins = [];
        foreach ($databaseContentLines as $line) {
            if ('#' === trim($line)[0]) {
                continue;
            }
            $this->plugins[] = $this->parseDatabaseEntry($line, (count($this->plugins) + 1));
        }
    }

    /**
     * @param string $entry
     * @param int    $number
     *
     * @return PluginInformation
     */
    private function parseDatabaseEntry(string $entry, int $number): PluginInformation
    {
        $fields = explode(self::DATABASE_DELIMITER, $entry);
        if (12 !== count($fields)) {
            throw new PluginDatabaseException('Plugindatabase is malformed, invalid number of fields found');
        }

        $plugin = new PluginInformation();
        $plugin->setNumber($number);
        $plugin->setName($fields[self::FIELD_INDEX_NAME]);
        $plugin->setTitle($fields[self::FIELD_INDEX_TITLE]);
        $plugin->setVersion($fields[self::FIELD_INDEX_VERSION]);
        $plugin->setFolderName($fields[self::FIELD_INDEX_FOLDER]);
        $plugin->setAuthorName($fields[self::FIELD_INDEX_AUTHOR_NAME]);
        $plugin->setAuthorEmail($fields[self::FIELD_INDEX_AUTHOR_EMAIL]);
        $plugin->setLogLevel($fields[self::FIELD_INDEX_LOGLEVEL] ?? null);
        $plugin->setAutoUpdate((int) ($fields[self::FIELD_INDEX_AUTOUPDATE] ?? 0));
        $plugin->setPreReleaseCfg($fields[self::FIELD_INDEX_PRERELEASECFG] ?? null);
        $plugin->setReleaseCfg($fields[self::FIELD_INDEX_RELEASECFG] ?? null);
        $plugin->setUiVersion($fields[self::FIELD_INDEX_UI_VERSION] ?? null);

        if (!$plugin->getChecksum() === $fields[self::FIELD_INDEX_MD5_CHECKSUM]) {
            throw new PluginDatabaseException(sprintf(
                'Plugin database entry for plugin %s seems to be corrupt, checksum is invalid',
                $plugin->getName()
            ));
        }

        return $plugin;
    }

    /**
     * Magic getter to allow direct access to plugin information.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!isset($arguments[0]) || !is_string($arguments[0])) {
            throw new \InvalidArgumentException('Invalid plugin name provided');
        }
        if ('get' !== substr($name, 0, 3) && 'is' !== substr($name, 0, 2)) {
            throw new PluginDatabaseException(sprintf(
                'Invalid method %s called, only getters are allowed',
                $name
            ));
        }

        $pluginInformation = $this->getPluginInformation($arguments[0]);
        if ($pluginInformation instanceof PluginInformation
            && method_exists($pluginInformation, $name)) {
            return $pluginInformation->{$name}();
        }

        throw new PluginDatabaseException(sprintf(
            'Invalid method %s called, method does not exist on plugin',
            $name
        ));
    }
}
