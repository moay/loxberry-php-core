<?php

namespace LoxBerry\ConfigurationParser;

/**
 * Interface ConfigurationParserInterface.
 */
interface ConfigurationParserInterface
{
    /**
     * @param string $section
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $section, string $key, $fallback = null);

    /**
     * @param string $section
     * @param string $key
     * @param $value
     */
    public function set(string $section, string $key, $value);

    /**
     * @param string      $section
     * @param string|null $key
     *
     * @return bool
     */
    public function has(string $section, ?string $key = null): bool;
}
