<?php

namespace LoxBerry\System;

use LoxBerry\Exceptions\PathProviderException;
use LoxBerry\Utility\LowLevel;

/**
 * Class PathProvider.
 */
class PathProvider
{
    const FALLBACK_HOME_DIR = '/opt/loxberry';

    const KNOWN_PATHS = [
        Paths::PATH_LB_HOME,
        Paths::PATH_LOG_DATABASE_FILE,
    ];

    /** @var LowLevel */
    private $lowLevel;

    /**
     * PathProvider constructor.
     *
     * @param LowLevel $lowLevel
     */
    public function __construct(LowLevel $lowLevel)
    {
        $this->lowLevel = $lowLevel;
    }

    /**
     * @param string $pathName
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
                return $this->getCombined($this->getLoxBerryHomePath(), Filenames::LOG_DATABASE_FILENAME);
        }
    }

    /**
     * @return string
     */
    private function getLoxBerryHomePath(): string
    {
        $environmentPath = $this->lowLevel->getEnvironmentVariable("LBHOMEDIR");
        if(is_string($environmentPath)) {
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
        return rtrim($path,'/').$fileName;
    }
}
