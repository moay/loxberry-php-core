<?php

namespace LoxBerry\System\Utility\HttpAccess;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\LowLevelExecutor;

/**
 * Class AbstractHttpAccess.
 */
abstract class AbstractHttpAccess
{
    /** @var SystemConfigurationParser */
    protected $systemConfiguration;

    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * HttpAccess constructor.
     *
     * @param SystemConfigurationParser $systemConfiguration
     * @param LowLevelExecutor          $lowLevel
     */
    public function __construct(
        SystemConfigurationParser $systemConfiguration,
        LowLevelExecutor $lowLevel
    ) {
        $this->systemConfiguration = $systemConfiguration;
        $this->lowLevel = $lowLevel;
    }

    abstract protected function getEndpointUrl(): string;

    /**
     * @param array $data
     *
     * @return string
     */
    public function getAsString(array $data): string
    {
        return $this->post($data);
    }

    /**
     * @param array $data
     *
     * @return array|null
     */
    public function getAsArray(array $data): ?array
    {
        $response = $this->getAsString($data);
        if (false === $response) {
            return null;
        }

        $decodedResponse = json_decode($response, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return null;
        }

        return $decodedResponse;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function execute(array $data)
    {
        return $this->post($data) || false;
    }

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

        return $this->lowLevel->fileGetContents($this->getEndpointUrl(), false, $context);
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return 'http://localhost:'.$this->systemConfiguration->getWebserverPort();
    }
}
