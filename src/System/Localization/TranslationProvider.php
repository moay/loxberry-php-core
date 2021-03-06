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
    const DEFAULT_TRANSLATION_FILE_PREFIX = 'language';

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
     * @param string|null $translationFileNamePrefix
     * @param string|null $language
     *
     * @return LanguageFileParser
     */
    public function getSystemTranslations(?string $translationFileNamePrefix = null, ?string $language = null): LanguageFileParser
    {
        $language = $language ?? $this->languageDeterminator->getLanguage();
        $translationFileNamePrefix = $translationFileNamePrefix ?? self::DEFAULT_TRANSLATION_FILE_PREFIX;
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
     * @param string      $pluginName
     * @param string|null $translationFileNamePrefix
     * @param string|null $language
     *
     * @return LanguageFileParser
     */
    public function getPluginTranslations(string $pluginName, ?string $translationFileNamePrefix = null, ?string $language = null): LanguageFileParser
    {
        $language = $language ?? $this->languageDeterminator->getLanguage();
        $translationFileNamePrefix = $translationFileNamePrefix ?? self::DEFAULT_TRANSLATION_FILE_PREFIX;
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
            $translationFile = $this->getFallbackFileName($directory, $translationFileNamePrefix);
        }

        if (!file_exists($translationFile) || !is_readable($translationFile)) {
            throw new \InvalidArgumentException(sprintf('Cannot find translation file %s nor fallback file %s.', $originalTranslationFile, $translationFile));
        }

        return $translationFile;
    }

    /**
     * @param string $directory
     * @param string $translationFileNamePrefix
     *
     * @return string
     */
    private function getFallbackFileName(string $directory, string $translationFileNamePrefix): string
    {
        return sprintf(
            '%s/%s_%s%s',
            $directory,
            $translationFileNamePrefix,
            self::FALLBACK_LANGUAGE,
            self::TRANSLATION_FILE_ENDING
        );
    }
}
