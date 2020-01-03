<?php

namespace LoxBerry\System\Utility\HttpAccess;

/**
 * Class AjaxStorageHandler.
 */
class AjaxStorageHandler extends AbstractHttpAccess
{
    const ENDPOINT_URL = '/admin/system/tools/ajax-storage-handler.cgi';

    // Flag and configuration names are derived from the current system. Should be optimized in the future.

    const CONFIGURATION_CURRENT_PATH = 'currentpath';
    const CONFIGURATION_CUSTOM_FOLDER = 'custom_folder';
    const CONFIGURATION_LABEL = 'label';

    const FLAG_WRITABLE_ONLY = 'readwriteonly';
    const FLAG_MINI = 'data_mini';
    const FLAG_TYPE_ALL = 'type_all';
    const FLAG_TYPE_USB = 'type_usb';
    const FLAG_TYPE_NET = 'type_net';
    const FLAG_TYPE_LOCAL = 'type_local';
    const FLAG_TYPE_CUSTOM = 'type_custom';

    const KNOWN_CONFIGURATIONS = [
        self::CONFIGURATION_CURRENT_PATH,
        self::CONFIGURATION_CUSTOM_FOLDER,
        self::CONFIGURATION_LABEL,
    ];
    const KNOWN_FLAGS = [
        self::FLAG_WRITABLE_ONLY,
        self::FLAG_MINI,
        self::FLAG_TYPE_ALL,
        self::FLAG_TYPE_USB,
        self::FLAG_TYPE_NET,
        self::FLAG_TYPE_LOCAL,
        self::FLAG_TYPE_CUSTOM,
    ];

    /**
     * @return string
     */
    protected function getEndpointUrl(): string
    {
        return $this->getBaseUrl().self::ENDPOINT_URL;
    }

    /**
     * @param string $formId
     * @param array  $configuration
     *
     * @return string
     */
    public function getStorageDeviceSelect(string $formId, array $flagsAndConfigurations = []): string
    {
        foreach ($flagsAndConfigurations as $configurationOrKey => $flagOrValue) {
            if (is_numeric($configurationOrKey)) {
                if (!in_array($flagOrValue, self::KNOWN_FLAGS)) {
                    throw new \InvalidArgumentException(sprintf('Unknown flag %s provided. Lookup documentation for the correct flags to use.', $flagOrValue));
                }
            } elseif (!in_array($configurationOrKey, self::KNOWN_CONFIGURATIONS)) {
                throw new \InvalidArgumentException(sprintf('Unknown configuration %s provided. Lookup documentation for the correct configurations to use.', $configurationOrKey));
            }
        }

        return $this->post(array_merge($this->prepareData($flagsAndConfigurations), [
            'action' => 'init',
            'formid' => $formId,
        ]));
    }

    /**
     * @param array $flagsAndConfigurations
     *
     * @return array
     */
    private function prepareData(array $flagsAndConfigurations): array
    {
        $data = [];
        foreach ($flagsAndConfigurations as $configurationOrKey => $flagOrValue) {
            if (is_numeric($configurationOrKey)) {
                $data[$flagOrValue] = 1;
            } else {
                $data[$configurationOrKey] = $flagOrValue;
            }
        }

        return $data;
    }
}
