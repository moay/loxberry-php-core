<?php

namespace LoxBerry\ConfigurationParser;

use LoxBerry\Exceptions\ConfigurationException;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;

/**
 * Class SystemConfigurationParser.
 */
class SystemConfigurationParser extends ConfigurationParser
{
    const SECTION_BASE = 'BASE';
    const SECTION_TIMESERVER = 'TIMESERVER';
    const SECTION_WEBSERVER = 'WEBSERVER';
    const SECTION_NETWORK = 'NETWORK';
    const SECTION_BINARIES = 'BINARIES';
    const SECTION_UPDATE = 'UPDATE';
    const SECTION_SSDP = 'SSDP';

    const SECTION_PREFIX_MINISERVER = 'MINISERVER';

    /** @var PathProvider */
    private $pathProvider;

    /** @var MiniserverInformation[] */
    private $miniservers = [];

    /**
     * SystemConfigurationParser constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;

        $configurationFile = $this->pathProvider->getPath(Paths::PATH_CENTRAL_CONFIG_FILE);
        if (!file_exists($configurationFile) || !is_readable($configurationFile)) {
            throw new ConfigurationException('Cannot read system configuration file.');
        }

        parent::__construct($configurationFile);
        $this->config->setQuoteStrings(false);

        for ($i = -1; $i <= count($this->miniservers); ++$i) {
            $identifier = self::SECTION_PREFIX_MINISERVER.($i + 2);
            if ($this->has($identifier)) {
                $this->parseMiniserverInformation($identifier);
            }
        }
    }

    /**
     * @return string
     */
    public function getLoxBerryVersion(): string
    {
        if (!$this->has(self::SECTION_BASE, 'VERSION')) {
            throw new \RuntimeException('LoxBerry version is not set in main config file');
        }

        return $this->get(self::SECTION_BASE, 'VERSION');
    }

    /**
     * @return string|null
     */
    public function getNetworkName(): ?string
    {
        return $this->get(self::SECTION_NETWORK, 'FRIENDLYNAME') ?? null;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->get(self::SECTION_BASE, 'LANG') ?? null;
    }

    /**
     * @return array
     */
    public function getBinaries(): array
    {
        return $this->config->get(self::SECTION_BINARIES) ?? [];
    }

    /**
     * @return int
     */
    public function getNumberOfMiniservers(): int
    {
        return count($this->miniservers);
    }

    /**
     * @return string
     */
    public function getCloudDnsAddress(): string
    {
        if (!$this->has(self::SECTION_BASE, 'CLOUDDNS')) {
            throw new \RuntimeException('CloudDNS address is not set in main config file');
        }

        return $this->get(self::SECTION_BASE, 'CLOUDDNS');
    }

    /**
     * @return int
     */
    public function getWebserverPort(): int
    {
        return $this->get(self::SECTION_WEBSERVER, 'PORT') ?? 80;
    }

    /**
     * @return MiniserverInformation[]
     */
    public function getMiniservers(): array
    {
        return $this->miniservers;
    }

    /**
     * @param $numberOrName
     *
     * @return MiniserverInformation|null
     */
    public function getMiniserver($ipOrName): ?MiniserverInformation
    {
        foreach ($this->miniservers as $miniserver) {
            if ($miniserver->getIpAddress() === $ipOrName || $miniserver->getName() === $ipOrName) {
                return $miniserver;
            }
        }

        return null;
    }

    /**
     * @throws ConfigurationException
     */
    public function set(string $section, string $key, $value)
    {
        throw new ConfigurationException('System variables are read only and not to be set via PHP library');
    }

    /**
     * @param string $identifier
     */
    private function parseMiniserverInformation(string $identifier)
    {
        $data = $this->config->get($identifier) ?? [];
        if (!count($data)) {
            return;
        }

        $miniserverInformation = new MiniserverInformation();
        $miniserverInformation->setName($data['NAME']);
        $miniserverInformation->setIpAddress($data['IPADDRESS']);
        $miniserverInformation->setPort((int) $data['PORT']);
        $miniserverInformation->setNote($data['NOTE']);
        $miniserverInformation->setAdminUsername($data['ADMIN']);
        $miniserverInformation->setAdminPassword($data['PASS'] ?? '');
        $miniserverInformation->setCloudUrl($data['CLOUDURL'] ?? null);
        $miniserverInformation->setCloudUrlFftPort($data['CLOUDURL'] ? (int) $data['CLOUDURL'] : null);
        $miniserverInformation->setUseCloudDns(ConfigurationParser::isEnabled($data['USECLOUDDNS'] ?? null));
        $miniserverInformation->setSecureGateway(ConfigurationParser::isEnabled($data['SECUREGATEWAY'] ?? null));
        $miniserverInformation->setEncryptResponse(ConfigurationParser::isEnabled($data['ENCRYPTRESPONSE'] ?? null));

        $this->miniservers[] = $miniserverInformation;
    }
}
