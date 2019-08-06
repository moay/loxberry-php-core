<?php

namespace LoxBerry\System;

use LoxBerry\Utility\LowLevel;

/**
 * Class PathProvider.
 */
class PathProvider
{
    const PATH_LB_HOME = 'LBHOMEDIR';

    const FALLBACK_HOME_DIR = '/opt/loxberry';

    const KNOWN_PATHS = [
        self::PATH_LB_HOME,
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
        switch ($pathName) {
            case self::PATH_LB_HOME:
                return $this->getLoxBerryHomePath();
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

        //            $this->logger->log('Home dir not properly set, falling back to /opt/loxberry', Logger::LEVEL_WARNING);

        return self::FALLBACK_HOME_DIR;
    }
}
