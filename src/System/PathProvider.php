<?php

namespace LoxBerry\System;

use LoxBerry\Exceptions\PathProviderException;

/**
 * Class PathProvider.
 */
class PathProvider
{
    const FALLBACK_HOME_DIR = '/opt/loxberry';

    const KNOWN_PATHS = [
        Paths::PATH_LB_HOME,
        Paths::PATH_SYSTEM_HTMLAUTH,
        Paths::PATH_SYSTEM_HTML,
        Paths::PATH_SYSTEM_TEMPLATE,
        Paths::PATH_SYSTEM_DATA,
        Paths::PATH_SYSTEM_LOG,
        Paths::PATH_SYSTEM_TMPFSLOG,
        Paths::PATH_SYSTEM_CONFIG,
        Paths::PATH_SYSTEM_SBIN,
        Paths::PATH_SYSTEM_BIN,
        Paths::PATH_LOG_DATABASE_FILE,
        Paths::PATH_PLUGIN_DATABASE_FILE,
        Paths::PATH_REBOOT_REQUIRED_FILE,
    ];

    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * PathProvider constructor.
     *
     * @param LowLevelExecutor $lowLevel
     */
    public function __construct(LowLevelExecutor $lowLevel)
    {
        $this->lowLevel = $lowLevel;
    }

    /**
     * @param string $pathName
     *
     * @return string
     */
    public function getPath(string $pathName): string
    {
        if (!in_array($pathName, self::KNOWN_PATHS)) {
            throw new PathProviderException(sprintf(
                'Unknown path %s requested',
                $pathName
            ));
        }

        switch ($pathName) {
            case Paths::PATH_LB_HOME:
                return $this->getLoxBerryHomePath();
            case Paths::PATH_LOG_DATABASE_FILE:
                return $this->getCombined($this->getLoxBerryHomePath(), FileNames::LOG_DATABASE_FILENAME);
            case Paths::PATH_PLUGIN_DATABASE_FILE:
                return $this->getCombined($this->getLoxBerryHomePath(), FileNames::PLUGIN_DATABASE_FILENAME);
            case Paths::PATH_REBOOT_REQUIRED_FILE:
                return $this->getCombined($this->getLoxBerryHomePath(), FileNames::REBOOT_REQUIRED_FILENAME);
            case Paths::PATH_SYSTEM_HTMLAUTH:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_HTMLAUTH);
            case Paths::PATH_SYSTEM_HTML:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_HTML);
            case Paths::PATH_SYSTEM_TEMPLATE:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_TEMPLATE);
            case Paths::PATH_SYSTEM_DATA:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_DATA);
            case Paths::PATH_SYSTEM_LOG:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_LOG);
            case Paths::PATH_SYSTEM_TMPFSLOG:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_TMPFSLOG);
            case Paths::PATH_SYSTEM_CONFIG:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_CONFIG);
            case Paths::PATH_SYSTEM_SBIN:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_SBIN);
            case Paths::PATH_SYSTEM_BIN:
                return $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_BIN);
        }
    }

    /**
     * @return string
     */
    private function getLoxBerryHomePath(): string
    {
        $environmentPath = $this->lowLevel->getEnvironmentVariable('LBHOMEDIR');
        if (is_string($environmentPath)) {
            return $environmentPath;
        }

        $userInfo = $this->lowLevel->getUserInfo('loxberry');
        if (null !== ($userInfo['uid']['dir'] ?? null)) {
            return $userInfo['uid']['dir'];
        }

        $this->lowLevel->errorLog('Home dir not properly set, falling back to /opt/loxberry');

        return self::FALLBACK_HOME_DIR;
    }

    /**
     * @param string $path
     * @param string $fileName
     *
     * @return string
     */
    private function getCombined(string $path, string $fileName): string
    {
        return rtrim($path, '/').DIRECTORY_SEPARATOR.$fileName;
    }
}
