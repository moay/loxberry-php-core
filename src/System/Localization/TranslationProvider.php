<?php

namespace LoxBerry\System\Localization;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;
use LoxBerry\System\Plugin\PluginPathProvider;

/**
 * Class TranslationProvider.
 */
class TranslationProvider
{
    const TRANSLATION_FILE_ENDING = '.ini';
    const FALLBACK_LANGUAGE = 'en';
    const LANGFILE_SUBDIRECTORY = '/lang';

    /** @var PathProvider */
    private $pathProvider;

    /** @var PluginPathProvider */
    private $pluginPathProvider;

    /** @var LanguageDeterminator */
    private $languageDeterminator;

    /** @var array */
    private $parsedTranslations = [];

    /**
     * TranslationProvider constructor.
     *
     * @param PathProvider         $pathProvider
     * @param PluginPathProvider   $pluginPathProvider
     * @param LanguageDeterminator $languageDeterminator
     */
    public function __construct(
        PathProvider $pathProvider,
        PluginPathProvider $pluginPathProvider,
        LanguageDeterminator $languageDeterminator
    ) {
        $this->pathProvider = $pathProvider;
        $this->pluginPathProvider = $pluginPathProvider;
        $this->languageDeterminator = $languageDeterminator;
    }

    /**
     * @param string $translationFileNamePrefix
     *
     * @return LanguageFileParser
     */
    public function getSystemTranslations(string $translationFileNamePrefix = 'language'): LanguageFileParser
    {
        $language = $this->languageDeterminator->getLanguage();
        if (
            array_key_exists($language, $this->parsedTranslations)
            && array_key_exists($translationFileNamePrefix, $this->parsedTranslations[$language]['system'])
        ) {
            return $this->parsedTranslations[$language]['system'][$translationFileNamePrefix];
        }
        $directory = $this->pathProvider->getPath(Paths::PATH_SYSTEM_TEMPLATE).self::LANGFILE_SUBDIRECTORY;
        $translationFile = $this->getTranslationFileName($directory, $translationFileNamePrefix, $language);
        $parser = new LanguageFileParser($translationFile);
        $this->parsedTranslations[$language]['system'][$translationFileNamePrefix] = $parser;

        return $parser;
    }

    /**
     * @param string $pluginName
     * @param string $translationFileNamePrefix
     *
     * @return LanguageFileParser
     */
    public function getPluginTranslations(string $pluginName, string $translationFileNamePrefix = 'language'): LanguageFileParser
    {
        $language = $this->languageDeterminator->getLanguage();
        if (
            array_key_exists($language, $this->parsedTranslations)
            && array_key_exists($pluginName, $this->parsedTranslations[$language])
            && array_key_exists($translationFileNamePrefix, $this->parsedTranslations[$language][$pluginName])
        ) {
            return $this->parsedTranslations[$language][$pluginName][$translationFileNamePrefix];
        }

        $this->pluginPathProvider->setPluginName($pluginName);
        $directory = $this->pluginPathProvider->getPath(Paths::PATH_PLUGIN_TEMPLATE).self::LANGFILE_SUBDIRECTORY;
        $translationFile = $this->getTranslationFileName($directory, $translationFileNamePrefix, $language);
        $parser = new LanguageFileParser($translationFile);
        $this->parsedTranslations[$language][$pluginName][$translationFileNamePrefix] = $parser;

        return $parser;
    }

    /**
     * @param string $directory
     * @param string $translationFileNamePrefix
     * @param string $language
     *
     * @return string
     */
    private function getTranslationFileName(string $directory, string $translationFileNamePrefix, string $language): string
    {
        $translationFile = sprintf(
            '%s/%s_%s%s',
            $directory,
            $translationFileNamePrefix,
            $language,
            self::TRANSLATION_FILE_ENDING
        );

        if (!file_exists($translationFile) || !is_readable($translationFile)) {
            $originalTranslationFile = $translationFile;
            $translationFile = sprintf(
                '%s/%s_%s%s',
                $directory,
                $translationFileNamePrefix,
                self::FALLBACK_LANGUAGE,
                self::TRANSLATION_FILE_ENDING
            );
        }

        if (!file_exists($translationFile) || !is_readable($translationFile)) {
            throw new \InvalidArgumentException(sprintf('Cannot find translation file %s nor fallback file %s.', $originalTranslationFile, $translationFile));
        }

        return $translationFile;
    }
}
