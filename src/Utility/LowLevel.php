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

    /**
     * @param string $message
     *
     * @return bool
     */
    public function errorLog(string $message): bool
    {
        return error_log($message);
    }

    /**
     * Enables static access to low level methods. Use only if injection is not possible.
     *
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = new LowLevel();
        if (method_exists($instance, $name)) {
            call_user_func_array([$instance, $name], $arguments);
        }
    }
}
