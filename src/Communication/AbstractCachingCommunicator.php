<?php

namespace LoxBerry\Communication;

use LoxBerry\Communication\ValueCache\AbstractValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;

/**
 * Class AbstractCachingCommunicator.
 */
abstract class AbstractCachingCommunicator
{
    private const CACHE_KEY_LAST_FULL_PUSH = 'UDP_CACHING_LAST_FULL_PUSH';
    private const CACHE_KEY_MINISERVER_LAN_PACKAGES = 'UDP_CACHING_TXP';
    private const CACHE_KEY_LAST_REBOOT_CHECK = 'UDP_CACHING_LAST_REBOOT_CHECK';

    private const FULL_PUSH_INTERVAL_DEFAULT = 3600;
    private const REBOOT_CHECK_INTERVAL = 300;

    /** @var int */
    private $fullPushInterval = self::FULL_PUSH_INTERVAL_DEFAULT;

    /** @var AbstractValueCache */
    protected $cache;

    /** @var Http */
    protected $http;

    /**
     * @param int $fullPushInterval
     */
    public function setFullPushInterval(int $fullPushInterval): void
    {
        $this->fullPushInterval = $fullPushInterval;
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param $data
     *
     * @return array
     */
    public function handleCaching(MiniserverInformation $miniserver, $data): array
    {
        if (!$this->fullPushRequired($miniserver) && !$this->miniserverRebooted($miniserver)) {
            $data = $this->filterChangedValues($miniserver, $data);
        } else {
            $this->cache->put(self::CACHE_KEY_LAST_FULL_PUSH, time(), $miniserver->getIpAddress(), $this->getCommunicatorPort());
        }

        return $data;
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param array                 $data
     *
     * @return array
     */
    private function filterChangedValues(MiniserverInformation $miniserver, array $data): array
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (!$this->cache->valueDiffersFromStored($key, $value, $miniserver->getIpAddress(), $this->getCommunicatorPort())) {
                continue;
            }

            $this->cache->put($key, $value, $miniserver->getIpAddress(), $this->getCommunicatorPort());
            $filteredData[$key] = $value;
        }

        return $filteredData;
    }

    /**
     * @param MiniserverInformation $miniserver
     *
     * @return bool
     */
    private function fullPushRequired(MiniserverInformation $miniserver): bool
    {
        $lastFullPush = $this->cache->get(self::CACHE_KEY_LAST_FULL_PUSH, $miniserver->getIpAddress(), $this->getCommunicatorPort());

        if (null === $lastFullPush) {
            return true;
        }

        return abs(time() - (int) $lastFullPush) > $this->fullPushInterval;
    }

    /**
     * @param MiniserverInformation $miniserver
     *
     * @return bool
     */
    private function miniserverRebooted(MiniserverInformation $miniserver): bool
    {
        $lastRebootCheck = $this->cache->get(self::CACHE_KEY_LAST_REBOOT_CHECK, $miniserver->getIpAddress(), $this->getCommunicatorPort());

        if (!is_int($lastRebootCheck) || (time() - $lastRebootCheck) > self::REBOOT_CHECK_INTERVAL) {
            $this->cache->put(self::CACHE_KEY_LAST_REBOOT_CHECK, time(), $miniserver->getIpAddress(), $this->getCommunicatorPort() ?? null);
            $lastKnownLanPackages = $this->cache->get(self::CACHE_KEY_MINISERVER_LAN_PACKAGES, $miniserver->getIpAddress(), $this->getCommunicatorPort());
            $currentLanPackages = $this->getCurrentLanPackages($miniserver);
            $this->cache->put(self::CACHE_KEY_MINISERVER_LAN_PACKAGES, $currentLanPackages, $miniserver->getIpAddress(), $this->getCommunicatorPort());

            if (is_int($lastKnownLanPackages) && $currentLanPackages < $lastKnownLanPackages) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MiniserverInformation $miniserver
     *
     * @return int
     */
    private function getCurrentLanPackages(MiniserverInformation $miniserver): int
    {
        if (!$this instanceof Http && !$this->http instanceof Http) {
            throw new \LogicException('Http communicator needed in order to get lan packages');
        }

        if ($this instanceof Http) {
            $status = $this->call($miniserver, '/dev/lan/txp');
        } else {
            $status = $this->http->call($miniserver, '/dev/lan/txp');
        }

        if (200 !== $status->getResponseCode()) {
            return 0;
        }

        return (int) $status->getContent();
    }

    /**
     * @return int|null
     */
    private function getCommunicatorPort(): ?int
    {
        return ($this instanceof Udp) ? $this->getUdpPort() : null;
    }
}
