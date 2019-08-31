<?php

namespace LoxBerry\System\Localization;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;

/**
 * Class LanguageDeterminator.
 */
class LanguageDeterminator
{
    /** @var SystemConfigurationParser */
    private $systemConfiguration;

    /**
     * LanguageDeterminator constructor.
     *
     * @param SystemConfigurationParser $systemConfiguration
     */
    public function __construct(SystemConfigurationParser $systemConfiguration)
    {
        $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        if (array_key_exists('lang', $_GET)) {
            return substr($_GET['lang'], 0, 2);
        }
        if (array_key_exists('lang', $_POST)) {
            return substr($_POST['lang'], 0, 2);
        }

        return $this->systemConfiguration->getLanguage() ?? 'en';
    }
}
