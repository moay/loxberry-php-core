<?php

namespace LoxBerry\ConfigurationParser;

/**
 * Class MiniserverInformation.
 */
class MiniserverInformation
{
    /** @var string */
    private $name;

    /** @var string */
    private $ipAddress;

    /** @var string */
    private $adminUsername;

    /** @var string */
    private $adminPassword;

    /** @var string|null */
    private $note;

    /** @var int|null */
    private $port;

    /** @var int|null */
    private $cloudUrlFftPort;

    /** @var string|null */
    private $cloudUrl;

    /** @var bool */
    private $secureGateway;

    /** @var bool */
    private $encryptResponse;

    /** @var bool */
    private $useCloudDns;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getAdminUsername(bool $raw = false): string
    {
        if ($raw) {
            return urldecode($this->adminUsername);
        }

        return $this->adminUsername;
    }

    /**
     * @param string $adminUsername
     */
    public function setAdminUsername(string $adminUsername): void
    {
        $this->adminUsername = $adminUsername;
    }

    /**
     * @return string
     */
    public function getAdminPassword(bool $raw = false): string
    {
        if ($raw) {
            return urldecode($this->adminPassword);
        }

        return $this->adminPassword;
    }

    /**
     * @param string $adminPassword
     */
    public function setAdminPassword(string $adminPassword): void
    {
        $this->adminPassword = $adminPassword;
    }

    public function getCredentials(bool $raw = false)
    {
        return implode(':', [
            $this->getAdminUsername($raw),
            $this->getAdminPassword($raw),
        ]);
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     */
    public function setPort(?int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return int|null
     */
    public function getCloudUrlFftPort(): ?int
    {
        return $this->cloudUrlFftPort;
    }

    /**
     * @param int|null $cloudUrlFftPort
     */
    public function setCloudUrlFftPort(?int $cloudUrlFftPort): void
    {
        $this->cloudUrlFftPort = $cloudUrlFftPort;
    }

    /**
     * @return string|null
     */
    public function getCloudUrl(): ?string
    {
        return $this->cloudUrl;
    }

    /**
     * @param string|null $cloudUrl
     */
    public function setCloudUrl(?string $cloudUrl): void
    {
        $this->cloudUrl = $cloudUrl;
    }

    /**
     * @return bool
     */
    public function isSecureGateway(): bool
    {
        return $this->secureGateway;
    }

    /**
     * @param bool $secureGateway
     */
    public function setSecureGateway(bool $secureGateway): void
    {
        $this->secureGateway = $secureGateway;
    }

    /**
     * @return bool
     */
    public function isEncryptResponse(): bool
    {
        return $this->encryptResponse;
    }

    /**
     * @param bool $encryptResponse
     */
    public function setEncryptResponse(bool $encryptResponse): void
    {
        $this->encryptResponse = $encryptResponse;
    }

    /**
     * @return bool
     */
    public function isUseCloudDns(): bool
    {
        return $this->useCloudDns;
    }

    /**
     * @param bool $useCloudDns
     */
    public function setUseCloudDns(bool $useCloudDns): void
    {
        $this->useCloudDns = $useCloudDns;
    }
}
