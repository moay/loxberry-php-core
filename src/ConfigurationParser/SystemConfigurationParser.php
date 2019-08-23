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

    /** @var array */
    private $configuration;

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

        while ($this->has($identifier = self::SECTION_PREFIX_MINISERVER.(count($this->miniservers) + 1))) {
            $this->parseMiniserverInformation($identifier);
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
     * @return int|null
     */
    public function getWebserverPort(): ?int
    {
        return $this->get(self::SECTION_WEBSERVER, 'PORT') ?? null;
    }

    /**
     * @param string $identifier
     */
    private function parseMiniserverInformation(string $identifier)
    {
        $data = $this->configuration[$identifier];

        $miniserverInformation = new MiniserverInformation();

        $this->miniservers[] = $miniserverInformation;
    }
}
