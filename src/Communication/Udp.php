<?php

namespace LoxBerry\Communication;

use LoxBerry\Communication\ValueCache\UdpValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\LowLevelExecutor;

/**
 * Class Udp.
 */
class Udp extends AbstractCachingCommunicator
{
    private const UDP_DELIMITER = '=';
    private const UDP_MAXLENGTH = 220;

    /** @var int */
    private $udpPort;

    /** @var LowLevelExecutor */
    private $lowLevel;

    /** @var resource */
    private $socket;

    /**
     * Udp constructor.
     *
     * @param UdpValueCache    $cache
     * @param LowLevelExecutor $lowLevel
     */
    public function __construct(UdpValueCache $cache, LowLevelExecutor $lowLevel, Http $http)
    {
        $this->cache = $cache;
        $this->lowLevel = $lowLevel;
        $this->http = $http;

        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param array                 $data
     * @param string|null           $prefix
     * @param bool                  $onlyChanged
     */
    public function push(MiniserverInformation $miniserver, array $data, ?string $prefix = null)
    {
        if (null === $this->udpPort) {
            throw new \LogicException('Cannot execute UDP call, port must be set first via setUdpPort');
        }

        if (count($data) > 0) {
            $messages = $this->prepareMessages($data, $prefix);
            foreach ($messages as $message) {
                if (null !== $prefix) {
                    $message = $prefix.': '.$message;
                }

                $this->sendViaUdp($miniserver, $message);
            }
        }
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param array                 $data
     * @param string|null           $prefix
     */
    public function pushChanged(MiniserverInformation $miniserver, array $data, ?string $prefix = null)
    {
        if (null === $this->udpPort) {
            throw new \LogicException('Cannot execute UDP call, port must be set first via setUdpPort');
        }

        $data = $this->handleCaching($miniserver, $data);

        $this->push($miniserver, $data, $prefix);
    }

    /**
     * @param int $udpPort
     */
    public function setUdpPort(int $udpPort): self
    {
        if ($udpPort < 0 || $udpPort > 65535) {
            throw new \InvalidArgumentException('Invalid UDP port provided. Port must be between 0 and 65535');
        }

        $this->udpPort = $udpPort;

        return $this;
    }

    /**
     * @return int
     */
    public function getUdpPort(): int
    {
        return $this->udpPort;
    }

    /**
     * @param array       $data
     * @param string|null $prefix
     *
     * @return array
     */
    private function prepareMessages(array $data, ?string $prefix = null): array
    {
        $messages = [];
        $prefixStrLength = null !== $prefix ? strlen($prefix) + 2 : 0;
        $message = '';

        foreach ($data as $key => $value) {
            $preparedValue = $key.self::UDP_DELIMITER.$value;
            if (($prefixStrLength + strlen($message.$preparedValue)) > self::UDP_MAXLENGTH) {
                $messages[] = $message;
                $message = '';
            }

            if (strlen($message.$preparedValue) > self::UDP_MAXLENGTH) {
                // Message too long for udp, skipping
                continue;
            }

            $message .= $preparedValue.' ';
        }
        $messages[] = rtrim($message);

        return $messages;
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param string                $message
     *
     * @return string
     */
    private function sendViaUdp(MiniserverInformation $miniserver, string $message)
    {
        $this->lowLevel->sendToSocket(
            $this->socket,
            $message,
            strlen($message),
            0,
            $miniserver->getIpAddress(),
            $this->udpPort
        );
    }
}
