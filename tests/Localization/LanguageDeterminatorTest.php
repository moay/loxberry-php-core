<?php

namespace LoxBerry\Tests\Localization;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\Localization\LanguageDeterminator;
use PHPUnit\Framework\TestCase;

/**
 * Class LanguageDeterminatorTest.
 */
class LanguageDeterminatorTest extends TestCase
{
    public function testReturnsLanguageFromGetData()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $_GET['lang'] = 'sv';

        $languageDeterminator = new LanguageDeterminator($systemConfigurationMock);
        $language = $languageDeterminator->getLanguage();

        unset($_GET['lang']);
        $this->assertEquals('sv', $language);
    }

    public function testReturnsLanguageFromPostData()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $_POST['lang'] = 'cz';

        $languageDeterminator = new LanguageDeterminator($systemConfigurationMock);
        $language = $languageDeterminator->getLanguage();

        unset($_POST['lang']);
        $this->assertEquals('cz', $language);
    }

    public function testReturnsLanguageFromSystemConfiguration()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $systemConfigurationMock->expects($this->once())
            ->method('getLanguage')
            ->willReturn('fr');

        $languageDeterminator = new LanguageDeterminator($systemConfigurationMock);
        $this->assertEquals('fr', $languageDeterminator->getLanguage());
    }

    public function testDefaultsToEnglish()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);

        $languageDeterminator = new LanguageDeterminator($systemConfigurationMock);
        $this->assertEquals('en', $languageDeterminator->getLanguage());
    }
}
