<?php

namespace LoxBerry\Utility;

/**
 * Class class LowLevelExecutor.
 *
 * Helper class to allow proper testing of the library without regard for low level issues
 */
class LowLevelExecutor
{
    const USERNAME = 'loxberry';

    /**
     * @param string $name
     *
     * @return array|false|string
     */
    public function getEnvironmentVariable(string $name)
    {
        return $this->execLowLevelFunction('getenv', $name);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getFileOwner(string $file): string
    {
        $fileOwner = $this->execLowLevelFunction('fileowner', $file);

        return $this->execLowLevelFunction('posix_getpwuid', $fileOwner)['name'];
    }

    /**
     * @param string $file
     * @param string $userName
     *
     * @return bool
     */
    public function setFileOwner(string $file, string $userName): bool
    {
        return $this->execLowLevelFunction('chown', [$file, $userName]);
    }

    /**
     * @param string $userName
     *
     * @return array
     */
    public function getUserInfo(string $userName)
    {
        $userInfo = $this->execLowLevelFunction('posix_getpwnam', $userName);

        return $this->execLowLevelFunction('posix_getpwuid', $userInfo);
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    public function errorLog(string $message): bool
    {
        return $this->execLowLevelFunction('error_log', $message);
    }

    /**
     * @param string $functionName
     * @param $arguments
     *
     * @return mixed
     */
    public function execLowLevelFunction(string $functionName, $arguments)
    {
        if (!function_exists($functionName)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot call low level function %s',
                $functionName
            ));
        }

        if (!is_array($arguments)) {
            return call_user_func($functionName, $arguments);
        }

        return call_user_func_array($functionName, $arguments);
    }

    /**
     * Enables static access to low level methods. Use only if injection is not possible.
     *
     * @param string $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, $arguments)
    {
        $instance = new self();
        if (method_exists($instance, $name)) {
            if (!is_array($arguments)) {
                return call_user_func([$instance, $name], $arguments);
            }

            return call_user_func_array([$instance, $name], $arguments);
        }
    }
}
