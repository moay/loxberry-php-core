<?php

namespace LoxBerry\System\Utility\HttpAccess;

/**
 * Class AjaxNotificationHandler.
 */
class AjaxNotificationHandler extends AbstractHttpAccess
{
    const ENDPOINT_URL = '/admin/system/tools/ajax-notification-handler.cgi';

    /**
     * @return string
     */
    protected function getEndpointUrl(): string
    {
        return $this->getBaseUrl().self::ENDPOINT_URL;
    }

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
}
