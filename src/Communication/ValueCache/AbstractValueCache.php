<?php

namespace LoxBerry\Communication\ValueCache;

use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\PathProvider;

/**
 * Class AbstractValueCache.
 */
abstract class AbstractValueCache
{
    /** @var string */
    private $cacheDirectory;

    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * UdpValueCache constructor.
     *
     * @param PathProvider     $pathProvider
     * @param LowLevelExecutor $lowLevel
     */
    public function __construct(PathProvider $pathProvider, LowLevelExecutor $lowLevel)
    {
        $this->cacheDirectory = $pathProvider->getPath($this->getCacheDirectory());
        $this->lowLevel = $lowLevel;
    }

    /**
     * @return string
     */
    abstract protected function getCacheDirectory(): string;

    /**
     * @param string   $miniserverIp
     * @param int|null $port
     *
     * @return string
     */
    abstract protected function getCacheFileName(string $miniserverIp, ?int $port = null): string;

    /**
     * @param string $key
     * @param $value
     * @param string   $miniserverIp
     * @param int|null $port
     */
    public function put(string $key, $value, string $miniserverIp, ?int $port = null)
    {
        $values = $this->read($miniserverIp, $port);
        $values[$key] = $value;

        $this->write($values, $miniserverIp, $port);
    }

    /**
     * @param string   $key
     * @param string   $miniserverIp
     * @param int|null $port
     *
     * @return mixed|null
     */
    public function get(string $key, string $miniserverIp, ?int $port = null)
    {
        $values = $this->read($miniserverIp, $port);

        return $values[$key] ?? null;
    }

    /**
     * @param string $key
     * @param $currentValue
     * @param string   $miniserverIp
     * @param int|null $port
     *
     * @return bool
     */
    public function valueDiffersFromStored(string $key, $currentValue, string $miniserverIp, ?int $port = null): bool
    {
        $values = $this->read($miniserverIp, $port);

        return !array_key_exists($key, $values) || $values[$key] !== $currentValue;
    }

    /**
     * @param string $miniserverIp
     * @param int    $udpPort
     *
     * @return array
     */
    private function read(string $miniserverIp, ?int $port): array
    {
        $this->prepareCacheDirectory();

        $fileName = $this->cacheDirectory.DIRECTORY_SEPARATOR.$this->getCacheFileName($miniserverIp, $port);
        if (!file_exists($fileName)) {
            return [];
        }

        $values = json_decode(file_get_contents($fileName), true);
        if (!is_array($values) || JSON_ERROR_NONE !== json_last_error()) {
            return [];
        }

        return $values;
    }

    /**
     * @param array    $values
     * @param string   $miniserverIp
     * @param int|null $port
     *
     * @return bool
     */
    protected function write(array $values, string $miniserverIp, ?int $port = null): bool
    {
        $this->prepareCacheDirectory();

        $fileName = $this->cacheDirectory.DIRECTORY_SEPARATOR.$this->getCacheFileName($miniserverIp, $port);
        if (!file_exists($fileName) && !touch($fileName)) {
            throw new \RuntimeException(sprintf(
                'Udp cache file "%s" could not be created',
                $fileName
            ));
        }

        return file_put_contents($fileName, json_encode($values, JSON_PRETTY_PRINT, 20))
            && $this->lowLevel->setFileOwner($fileName, LowLevelExecutor::USERNAME);
    }

    private function prepareCacheDirectory()
    {
        if (!file_exists($cacheDirectory = $this->cacheDirectory) && !mkdir($cacheDirectory) && !is_dir($cacheDirectory)) {
            throw new \RuntimeException(sprintf(
                'Udp cache directory "%s" could not be created',
                $cacheDirectory
            ));
        }
    }
}
