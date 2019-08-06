<?php

namespace LoxBerry\ConfigurationParser;

use LoxBerry\Exceptions\ConfigurationException;

/**
 * Class ConfigurationParser.
 */
class ConfigurationParser
{
    /** @var \Config_Lite */
    private $config;

    /**
     * ConfigurationParser constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new ConfigurationException(sprintf(
                'Configuration file %s does not exist',
                $fileName
            ));
        }
        $this->config = new \Config_Lite($fileName);
    }

    /**
     * @param string $section
     * @param string $key
     *
     * @return mixed
     *
     * @throws \Config_Lite_Exception
     */
    public function get(string $section, string $key, $fallback = null)
    {
        return $this->config->get($section, $key, $fallback);
    }

    /**
     * @param string $section
     * @param string $key
     * @param $value
     *
     * @throws \Config_Lite_Exception
     */
    public function set(string $section, string $key, $value)
    {
        $this->config->set($section, $key, $value);
        $this->config->save();
    }

    /**
     * @param string $section
     * @param string|null $key
     *
     * @return bool
     */
    public function has(string $section, ?string $key = null): bool
    {
        if (null === $key) {
            return $this->config->hasSection($section);
        }

        return $this->config->has($section, $key);
    }
}
