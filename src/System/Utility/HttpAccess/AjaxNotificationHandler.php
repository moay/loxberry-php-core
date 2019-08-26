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
}
