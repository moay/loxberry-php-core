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
                $resolvedPath = $this->getLoxBerryHomePath();
                break;
            case Paths::PATH_LOG_DATABASE_FILE:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), FileNames::LOG_DATABASE_FILENAME);
                break;
            case Paths::PATH_PLUGIN_DATABASE_FILE:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), FileNames::PLUGIN_DATABASE_FILENAME);
                break;
            case Paths::PATH_REBOOT_REQUIRED_FILE:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), FileNames::REBOOT_REQUIRED_FILENAME);
                break;
            case Paths::PATH_SYSTEM_HTMLAUTH:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_HTMLAUTH);
                break;
            case Paths::PATH_SYSTEM_HTML:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_HTML);
                break;
            case Paths::PATH_SYSTEM_TEMPLATE:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_TEMPLATE);
                break;
            case Paths::PATH_SYSTEM_DATA:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_DATA);
                break;
            case Paths::PATH_SYSTEM_LOG:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_LOG);
                break;
            case Paths::PATH_SYSTEM_TMPFSLOG:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_TMPFSLOG);
                break;
            case Paths::PATH_SYSTEM_CONFIG:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_CONFIG);
                break;
            case Paths::PATH_SYSTEM_SBIN:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_SBIN);
                break;
            case Paths::PATH_SYSTEM_BIN:
                $resolvedPath = $this->getCombined($this->getLoxBerryHomePath(), DirectoryNames::SYSTEM_BIN);
                break;
        }

        return $resolvedPath;
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
