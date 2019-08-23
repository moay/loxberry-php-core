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

        if (Paths::PATH_LB_HOME === $pathName) {
            return $this->getLoxBerryHomePath();
        }

        return $this->resolveCombinedPath($pathName);
    }

    /**
     * @return string
     */
    private function getLoxBerryHomePath(): string
    {
        $environmentPath = $this->lowLevel->getEnvironmentVariable('LBHOMEDIR');
        if (is_string($environmentPath)) {
            return rtrim($environmentPath, '/');
        }

        $userInfo = $this->lowLevel->getUserInfo('loxberry');
        if (null !== ($userInfo['uid']['dir'] ?? null)) {
            return rtrim($userInfo['uid']['dir'], '/');
        }

        $this->lowLevel->errorLog('Home dir not properly set, falling back to /opt/loxberry');

        return self::FALLBACK_HOME_DIR;
    }

    /**
     * @param string $pathToResolve
     *
     * @return string
     */
    private function resolveCombinedPath(string $pathToResolve): string
    {
        $pathMap = [
            Paths::PATH_LOG_DATABASE_FILE => FileNames::LOG_DATABASE_FILENAME,
            Paths::PATH_PLUGIN_DATABASE_FILE => FileNames::PLUGIN_DATABASE_FILENAME,
            Paths::PATH_REBOOT_REQUIRED_FILE => FileNames::REBOOT_REQUIRED_FILENAME,
            Paths::PATH_SYSTEM_HTMLAUTH => DirectoryNames::SYSTEM_HTMLAUTH,
            Paths::PATH_SYSTEM_HTML => DirectoryNames::SYSTEM_HTML,
            Paths::PATH_SYSTEM_TEMPLATE => DirectoryNames::SYSTEM_TEMPLATE,
            Paths::PATH_SYSTEM_DATA => DirectoryNames::SYSTEM_DATA,
            Paths::PATH_SYSTEM_LOG => DirectoryNames::SYSTEM_LOG,
            Paths::PATH_SYSTEM_TMPFSLOG => DirectoryNames::SYSTEM_TMPFSLOG,
            Paths::PATH_SYSTEM_CONFIG => DirectoryNames::SYSTEM_CONFIG,
            Paths::PATH_SYSTEM_SBIN => DirectoryNames::SYSTEM_SBIN,
            Paths::PATH_SYSTEM_BIN => DirectoryNames::SYSTEM_BIN,
        ];

        return $this->getLoxBerryHomePath().DIRECTORY_SEPARATOR.$pathMap[$pathToResolve];
    }
}
