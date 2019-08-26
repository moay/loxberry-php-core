<?php

namespace LoxBerry\Communication\Udp;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;

/**
 * Class UdpValueCache.
 */
class UdpValueCache
{
    const CACHE_FILENAME_TEMPLATE = 'msudp_mem_%s_%s.json';

    /** @var string */
    private $cacheDirectory;

    /**
     * UdpValueCache constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->cacheDirectory = $pathProvider->getPath(Paths::PATH_SYSTEM_UDP_CACHE);
    }

    /**
     * @param string $key
     * @param $value
     * @param string $miniserverIp
     * @param int    $udpPort
     */
    public function storeValue(string $key, $value, string $miniserverIp, int $udpPort)
    {
        $values = $this->read($miniserverIp, $udpPort);
        $values[$key] = $value;

        $this->write($values, $miniserverIp, $udpPort);
    }

    /**
     * @param string $key
     * @param $currentValue
     * @param string $miniserverIp
     * @param int    $udpPort
     *
     * @return bool
     */
    public function valueDiffersFromStored(string $key, $currentValue, string $miniserverIp, int $udpPort): bool
    {
        $values = $this->read($miniserverIp, $udpPort);

        return !array_key_exists($key, $values) || $values[$key] !== $currentValue;
    }

    /**
     * @param string $miniserverIp
     * @param int    $udpPort
     *
     * @return array
     */
    private function read(string $miniserverIp, int $udpPort): array
    {
        $this->prepareCacheDirectory();

        $fileName = $this->getFileName($miniserverIp, $udpPort);
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
     * @param string $miniserverIp
     * @param int    $udpPort
     *
     * @return array
     */
    private function write(array $values, string $miniserverIp, int $udpPort): bool
    {
        $this->prepareCacheDirectory();

        $fileName = $this->getFileName($miniserverIp, $udpPort);
        if (!file_exists($fileName) && !touch($fileName)) {
            throw new \RuntimeException(sprintf(
                'Udp cache file "%s" could not be created',
                $fileName
            ));
        }

        return false !== file_put_contents($fileName, json_encode($values));
    }

    /**
     * @param string $miniserverIp
     * @param int    $udpPort
     *
     * @return string
     */
    private function getFileName(string $miniserverIp, int $udpPort): string
    {
        return $this->cacheDirectory.DIRECTORY_SEPARATOR.sprintf(
            self::CACHE_FILENAME_TEMPLATE,
            md5($miniserverIp),
            $udpPort
        );
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
