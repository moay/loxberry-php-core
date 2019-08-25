<?php

namespace LoxBerry\Utility;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;

/**
 * Class AbstractHttpAccess.
 */
abstract class AbstractHttpAccess
{
    /** @var SystemConfigurationParser */
    protected $systemConfiguration;

    /**
     * HttpAccess constructor.
     *
     * @param SystemConfigurationParser $systemConfiguration
     */
    public function __construct(SystemConfigurationParser $systemConfiguration)
    {
        $this->systemConfiguration = $systemConfiguration;
    }

    abstract protected function getEndpointUrl(): string;

    /**
     * @param array $data
     *
     * @return false|string
     */
    protected function post(array $data)
    {
        $context = stream_context_create([
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ]);

        return file_get_contents($this->getEndpointUrl(), false, $context);
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return 'http://localhost:'.($this->systemConfiguration->getWebserverPort() ?? 80);
    }
}
