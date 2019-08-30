<?php

namespace LoxBerry\System\Utility;

use LoxBerry\Communication\Http;
use LoxBerry\ConfigurationParser\MiniserverInformation;

/**
 * Class FtpPortDeterminator.
 */
class FtpPortDeterminator
{
    /** @var Http */
    private $http;

    /**
     * FtpPortDeterminator constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * @param MiniserverInformation $miniserver
     */
    public function getFtpPort(MiniserverInformation $miniserver): int
    {
        if ($miniserver->isUseCloudDns() && null !== $miniserver->getCloudUrlFftPort()) {
            return $miniserver->getCloudUrlFftPort();
        }

        $informationFromServer = $this->http->call($miniserver, '/dev/cfg/ftp');
        if (200 !== $informationFromServer->getResponseCode()) {
            throw new \RuntimeException('Cannot get FTP port from miniserver');
        }

        return (int) $informationFromServer->getValue();
    }
}
