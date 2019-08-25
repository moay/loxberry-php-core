<?php

namespace LoxBerry\Utility\HttpAccess;

use LoxBerry\Utility\AbstractHttpAccess;

/**
 * Class AjaxNotificationHandler.
 */
class AjaxNotificationHandler extends AbstractHttpAccess
{
    const ENDPOINT_URL = '/admin/system/tools/ajax-notification-handler.cgi';

    /**
     * @param array $data
     *
     * @return string
     */
    public function getAsString(array $data): string
    {
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getAsArray(array $data): array
    {
    }

    /**
     * @return string
     */
    protected function getEndpointUrl(): string
    {
        return $this->getBaseUrl().self::ENDPOINT_URL;
    }
}
