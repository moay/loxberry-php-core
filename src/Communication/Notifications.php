<?php

namespace LoxBerry\Communication;

use LoxBerry\System\Plugin\PluginDatabase;
use LoxBerry\System\Utility\HttpAccess\AjaxNotificationHandler;

/**
 * Class Notifications.
 */
class Notifications
{
    const RETRIEVE_ALL = 'all';
    const RETRIEVE_ERRORS = 'error';
    const RETRIEVE_INFO = 'info';

    const SEVERITY_LEVEL_INFO = 6;
    const SEVERITY_LEVEL_ERROR = 3;

    /** @var AjaxNotificationHandler */
    private $ajaxNotificationHandler;

    /** @var PluginDatabase */
    private $pluginDatabase;

    /**
     * Notifications constructor.
     *
     * @param AjaxNotificationHandler $ajaxNotificationHandler
     * @param PluginDatabase          $pluginDatabase
     */
    public function __construct(AjaxNotificationHandler $ajaxNotificationHandler, PluginDatabase $pluginDatabase)
    {
        $this->ajaxNotificationHandler = $ajaxNotificationHandler;
        $this->pluginDatabase = $pluginDatabase;
    }

    /**
     * @param string|null $packageName
     * @param string|null $name
     *
     * @return array|null
     */
    public function get(?string $packageName = null, ?string $name = null): ?array
    {
        $data = ['action' => 'get_notifications'];

        if (null !== $packageName) {
            $data['package'] = $packageName;
        }
        if (null !== $name) {
            $data['name'] = $name;
        }

        return $this->ajaxNotificationHandler->getAsArray($data);
    }

    /**
     * @param string|null $packageName
     * @param string|null $name
     * @param string      $type
     *
     * @return string|bool
     */
    public function getHtml(?string $packageName = null, ?string $name = null, string $type = 'all')
    {
        if (!in_array($type, [
            self::RETRIEVE_ALL,
            self::RETRIEVE_ERRORS,
            self::RETRIEVE_INFO,
        ])) {
            throw new \InvalidArgumentException('Notifications type must be one of "all", "error" or "info"');
        }

        $data = [
            'action' => 'get_notifications_html',
            'type' => $type,
        ];

        if (null !== $packageName) {
            $data['package'] = $packageName;
        }
        if (null !== $name) {
            $data['name'] = $name;
        }

        return $this->ajaxNotificationHandler->getAsString($data);
    }

    /**
     * @param string|null $packageName
     * @param string|null $name
     * @param string|null $message
     * @param array       $additionalData
     * @param bool        $isError
     *
     * @return bool
     */
    public function notify(
        string $packageName,
        string $name,
        string $message,
        array $additionalData = [],
        $isError = false
    ): bool {
        $data = [
            'action' => 'notifyext',
            'severity' => $isError ? 6 : 3,
            'package' => $packageName,
            'name' => $name,
            'message' => $message,
            $this->pluginDatabase->isInstalledPlugin($packageName) ? '_ISPLUGIN' : '_ISSYSTEM' => 1,
        ];

        return $this->ajaxNotificationHandler->execute(array_merge($data, $additionalData));
    }
}
