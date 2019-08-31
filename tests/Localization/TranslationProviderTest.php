<?php

namespace LoxBerry\Tests\Localization;

use LoxBerry\System\Localization\LanguageDeterminator;
use LoxBerry\System\Localization\LanguageFileParser;
use LoxBerry\System\Localization\TranslationProvider;
use LoxBerry\System\PathProvider;
use LoxBerry\System\Plugin\PluginPathProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class TranslationProviderTest.
 */
class TranslationProviderTest extends TestCase
{
    public function testLoadsSystemTranslations()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProviderMock = $this->createMock(PluginPathProvider::class);
        $languageDeterminatorMock = $this->createMock(LanguageDeterminator::class);

        $languageDeterminatorMock->method('getLanguage')->willReturn('de');
        $pathProviderMock->method('getPath')->willReturn(__DIR__.'/resources');

        $translationProvider = new TranslationProvider($pathProviderMock, $pluginPathProviderMock, $languageDeterminatorMock);
        $systemTranslations = $translationProvider->getSystemTranslations('language');
        $this->assertInstanceOf(LanguageFileParser::class, $systemTranslations);
        $this->assertEquals('Alles OK!', $systemTranslations->getTranslated('COMMON', 'MSG_ALLOK'));
    }

    public function testLoadsSystemTranslationsFallback()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProviderMock = $this->createMock(PluginPathProvider::class);
        $languageDeterminatorMock = $this->createMock(LanguageDeterminator::class);

        $languageDeterminatorMock->method('getLanguage')->willReturn('fr');
        $pathProviderMock->method('getPath')->willReturn(__DIR__.'/resources');

        $translationProvider = new TranslationProvider($pathProviderMock, $pluginPathProviderMock, $languageDeterminatorMock);
        $systemTranslations = $translationProvider->getSystemTranslations('language');
        $this->assertInstanceOf(LanguageFileParser::class, $systemTranslations);
        $this->assertEquals('Everything ok!', $systemTranslations->getTranslated('COMMON', 'MSG_ALLOK'));
    }

    public function testLoadsPluginTranslations()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProviderMock = $this->createMock(PluginPathProvider::class);
        $languageDeterminatorMock = $this->createMock(LanguageDeterminator::class);

        $languageDeterminatorMock->method('getLanguage')->willReturn('de');
        $pluginPathProviderMock->method('getPath')->willReturn(__DIR__.'/resources');

        $translationProvider = new TranslationProvider($pathProviderMock, $pluginPathProviderMock, $languageDeterminatorMock);
        $pluginTranslations = $translationProvider->getPluginTranslations('testPlugin', 'language');
        $this->assertInstanceOf(LanguageFileParser::class, $pluginTranslations);
        $this->assertEquals('Alles OK!', $pluginTranslations->getTranslated('COMMON', 'MSG_ALLOK'));
    }

    public function testLoadsPluginTranslationsFallback()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pluginPathProviderMock = $this->createMock(PluginPathProvider::class);
        $languageDeterminatorMock = $this->createMock(LanguageDeterminator::class);

        $languageDeterminatorMock->method('getLanguage')->willReturn('fr');
        $pluginPathProviderMock->method('getPath')->willReturn(__DIR__.'/resources');

        $translationProvider = new TranslationProvider($pathProviderMock, $pluginPathProviderMock, $languageDeterminatorMock);
        $pluginTranslations = $translationProvider->getPluginTranslations('testPlugin', 'language');
        $this->assertInstanceOf(LanguageFileParser::class, $pluginTranslations);
        $this->assertEquals('Everything ok!', $pluginTranslations->getTranslated('COMMON', 'MSG_ALLOK'));
    }
}
