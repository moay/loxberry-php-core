<?php

namespace LoxBerry\Communication;

use LoxBerry\Communication\ValueCache\HttpValueCache;
use LoxBerry\Communication\ValueCache\UdpValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\LowLevelExecutor;

/**
 * Class Http.
 */
class Http extends AbstractCachingCommunicator
{
    /**
     * Udp constructor.
     *
     * @param UdpValueCache    $cache
     * @param LowLevelExecutor $lowLevelExecutor
     */
    public function __construct(HttpValueCache $cache, LowLevelExecutor $lowLevelExecutor)
    {
        $this->cache = $cache;
        $this->lowLevelExecutor = $lowLevelExecutor;

        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    public function blockAction(MiniserverInformation $miniserver, array $blocks): array
    {
        // Todo: test and implement
    }

    public function call(MiniserverInformation $miniserver, string $command): HttpResponse
    {
        // Todo: test and implement
    }

    public function send(MiniserverInformation $miniserver, array $data)
    {
        // Todo: test and implement
    }

    public function sendChanged(MiniserverInformation $miniserver, array $data)
    {
        $data = $this->handleCaching($miniserver, $data);

        $this->send($miniserver, $data);
    }
}
