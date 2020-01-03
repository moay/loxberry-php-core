<?php

namespace LoxBerry\System\Plugin;

use LoxBerry\System\PathProvider;

/**
 * Class PluginInformation.
 */
class PluginInformation
{
    const DEFAULT_ICON = 'icon_64.png';

    /** @var int */
    private $number;

    /** @var string */
    private $authorName;

    /** @var string */
    private $authorEmail;

    /** @var string */
    private $version;

    /** @var string */
    private $name;

    /** @var string */
    private $folderName;

    /** @var string */
    private $title;

    /** @var string|null */
    private $uiVersion;

    /** @var int|null */
    private $autoUpdate;

    /** @var string|null */
    private $releaseCfg;

    /** @var string|null */
    private $preReleaseCfg;

    /** @var int|null */
    private $logLevel;

    /** @var bool */
    private $logLevelsEnabled = true;

    /** @var \DateTimeImmutable */
    private $installedAt;

    /** @var array|string[] */
    private $directories;

    /** @var array|string[] */
    private $files;

    /** @var PluginPathProvider */
    private $pluginPathProvider;

    /**
     * PluginInformation constructor.
     *
     * @param PluginPathProvider $pluginPathProvider
     */
    public function __construct(PluginPathProvider $pluginPathProvider)
    {
        $this->pluginPathProvider = $pluginPathProvider;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     */
    public function setAuthorEmail(string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

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
        $this->pluginPathProvider->setPluginName($name);
    }

    /**
     * @return string
     */
    public function getFolderName(): string
    {
        return $this->folderName;
    }

    /**
     * @param string $folderName
     */
    public function setFolderName(string $folderName): void
    {
        $this->folderName = $folderName;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getUiVersion(): ?string
    {
        return $this->uiVersion;
    }

    /**
     * @param string|null $uiVersion
     */
    public function setUiVersion(?string $uiVersion): void
    {
        $this->uiVersion = $uiVersion;
    }

    /**
     * @return int|null
     */
    public function getAutoUpdate(): ?int
    {
        return $this->autoUpdate;
    }

    /**
     * @param int|null $autoUpdate
     */
    public function setAutoUpdate(?int $autoUpdate): void
    {
        $this->autoUpdate = $autoUpdate;
    }

    /**
     * @return string|null
     */
    public function getReleaseCfg(): ?string
    {
        return $this->releaseCfg;
    }

    /**
     * @param string|null $releaseCfg
     */
    public function setReleaseCfg(?string $releaseCfg): void
    {
        $this->releaseCfg = $releaseCfg;
    }

    /**
     * @return string|null
     */
    public function getPreReleaseCfg(): ?string
    {
        return $this->preReleaseCfg;
    }

    /**
     * @param string|null $preReleaseCfg
     */
    public function setPreReleaseCfg(?string $preReleaseCfg): void
    {
        $this->preReleaseCfg = $preReleaseCfg;
    }

    /**
     * @return int|null
     */
    public function getLogLevel(): ?int
    {
        return $this->logLevel;
    }

    /**
     * @param int|null $logLevel
     */
    public function setLogLevel(?int $logLevel): void
    {
        if (-1 === $logLevel) {
            $logLevel = null;
        }
        $this->logLevel = $logLevel;
    }

    /**
     * @return bool
     */
    public function isLogLevelsEnabled(): bool
    {
        return $this->logLevelsEnabled;
    }

    /**
     * @param bool $logLevelsEnabled
     */
    public function setLogLevelsEnabled(bool $logLevelsEnabled): void
    {
        $this->logLevelsEnabled = $logLevelsEnabled;
    }

    /**
     * @return string
     */
    public function getChecksum(): string
    {
        return md5($this->authorName.$this->authorEmail.$this->name.$this->folderName);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getInstalledAt(): \DateTimeImmutable
    {
        return $this->installedAt;
    }

    /**
     * @param \DateTimeImmutable $installedAt
     */
    public function setInstalledAt(\DateTimeImmutable $installedAt): void
    {
        $this->installedAt = $installedAt;
    }

    /**
     * @return array|string[]
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @param array|string[] $directories
     */
    public function setDirectories($directories): void
    {
        $this->directories = $directories;
    }

    /**
     * @return array|string[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array|string[] $files
     */
    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * @param string $pathName
     *
     * @return mixed|string
     */
    public function getFile(string $fileName)
    {
        return $this->files[$fileName];
    }

    /**
     * @param string $pathName
     *
     * @return mixed|string
     */
    public function getPath(string $pathName)
    {
        return $this->directories[$pathName] ?? $this->pluginPathProvider->getPath(strtoupper($pathName));
    }

    /**
     * @return string
     */
    public function getIconPath(): string
    {
        return DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, [
            'system',
            'images',
            'icons',
            $this->name,
            self::DEFAULT_ICON,
        ]);
    }
}
