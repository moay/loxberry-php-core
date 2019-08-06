<?php

namespace LoxBerry\Utility;

/**
 * Class LowLevel.
 *
 * Helper class to allow proper testing of the library without regard for low level issues
 */
class LowLevel
{
    /**
     * @param string $name
     *
     * @return array|false|string
     */
    public function getEnvironmentVariable(string $name)
    {
        return getenv($name);
    }

    /**
     * @param string $userName
     *
     * @return array
     */
    public function getUserInfo(string $userName)
    {
        return posix_getpwuid(posix_getpwnam($userName));
    }
}
