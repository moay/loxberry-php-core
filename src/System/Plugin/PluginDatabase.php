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
    const FIELD_NAME_MD5_CHECKSUM = 'md5';
    const FIELD_NAME_AUTHOR_NAME = 'author_name';
    const FIELD_NAME_AUTHOR_EMAIL = 'author_email';
    const FIELD_NAME_VERSION = 'version';
    const FIELD_NAME_NAME = 'name';
    const FIELD_NAME_FOLDER = 'folder';
    const FIELD_NAME_TITLE = 'title';
    const FIELD_NAME_UI_VERSION = 'interface';
    const FIELD_NAME_AUTOUPDATE = 'autoupdate';
    const FIELD_NAME_RELEASECFG = 'releasecfg';
    const FIELD_NAME_PRERELEASECFG = 'prereleasecfg';
    const FIELD_NAME_LOGLEVEL = 'loglevel';
    const FIELD_NAME_LOGLEVELS_ENABLED = 'loglevels_enabled';
    const FIELD_NAME_INSTALLED_AT = 'epoch_firstinstalled';
    const FIELD_NAME_DIRECTORIES = 'directories';
    const FIELD_NAME_FILES = 'files';

    /** @var PathProvider */
    private $pathProvider;

    /** @var PluginInformation[]|array */
    private $plugins;

    /**
     * PluginDatabase constructor.
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

        throw new PluginDatabaseException(sprintf('Plugin "%s" is not installed', $pluginName));
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

        $this->plugins = [];

        $parsedFileContent = json_decode(file_get_contents($databaseFileName), true)['plugins'] ?? [];
        foreach ($parsedFileContent as $pluginInfo) {
            $this->plugins[] = $this->parseDatabaseEntry($pluginInfo, (count($this->plugins) + 1));
        }
    }

    /**
     * @param array $fields
     * @param int   $number
     *
     * @return PluginInformation
     */
    private function parseDatabaseEntry(array $fields, int $number): PluginInformation
    {
        if (12 > count($fields)) {
            throw new PluginDatabaseException('Plugindatabase is malformed, invalid number of fields found');
        }

        $plugin = new PluginInformation(new PluginPathProvider($this->pathProvider));
        $plugin->setNumber($number);
        $plugin->setName($fields[self::FIELD_NAME_NAME]);
        $plugin->setTitle($fields[self::FIELD_NAME_TITLE]);
        $plugin->setVersion($fields[self::FIELD_NAME_VERSION]);
        $plugin->setFolderName($fields[self::FIELD_NAME_FOLDER]);
        $plugin->setAuthorName($fields[self::FIELD_NAME_AUTHOR_NAME]);
        $plugin->setAuthorEmail($fields[self::FIELD_NAME_AUTHOR_EMAIL]);
        $plugin->setLogLevel($fields[self::FIELD_NAME_LOGLEVEL] ?? null);
        $plugin->setAutoUpdate((int) ($fields[self::FIELD_NAME_AUTOUPDATE] ?? 0));
        $plugin->setPreReleaseCfg($fields[self::FIELD_NAME_PRERELEASECFG] ?? null);
        $plugin->setReleaseCfg($fields[self::FIELD_NAME_RELEASECFG] ?? null);
        $plugin->setUiVersion($fields[self::FIELD_NAME_UI_VERSION] ?? null);
        $plugin->setLogLevelsEnabled($fields[self::FIELD_NAME_LOGLEVELS_ENABLED] ?? true);
        $plugin->setInstalledAt((new \DateTimeImmutable())->setTimestamp($fields[self::FIELD_NAME_INSTALLED_AT]));
        $plugin->setDirectories($fields[self::FIELD_NAME_DIRECTORIES] ?? []);
        $plugin->setFiles($fields[self::FIELD_NAME_FILES] ?? []);

        if (!$plugin->getChecksum() === $fields[self::FIELD_NAME_MD5_CHECKSUM]) {
            throw new PluginDatabaseException(sprintf('Plugin database entry for plugin %s seems to be corrupt, checksum is invalid', $plugin->getName()));
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
            throw new PluginDatabaseException(sprintf('Invalid method %s called, only getters are allowed', $name));
        }

        $pluginInformation = $this->getPluginInformation($arguments[0]);
        if ($pluginInformation instanceof PluginInformation
            && method_exists($pluginInformation, $name)) {
            return $pluginInformation->{$name}();
        }

        throw new PluginDatabaseException(sprintf('Invalid method %s called, method does not exist on plugin', $name));
    }
}
