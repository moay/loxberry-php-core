<?php

namespace LoxBerry\Tests\Localization;

use LoxBerry\System\Localization\LanguageFileParser;
use PHPUnit\Framework\TestCase;

/**
 * Class LanguageFileParserTest.
 */
class LanguageFileParserTest extends TestCase
{
    const TEST_FILE = __DIR__.'/resources/lang/language_en.ini';

    /**
     * @dataProvider validTranslations
     */
    public function testLoadsAndTranslatesSectionAndKey($section, $key, $expectedTranslation)
    {
        $parser = new LanguageFileParser(self::TEST_FILE);
        $this->assertSame($expectedTranslation, $parser->getTranslated($section, $key));
    }

    /**
     * @dataProvider validTranslations
     */
    public function testLoadsAndTranslatesSectionKeyCombined($section, $key, $expectedTranslation)
    {
        $parser = new LanguageFileParser(self::TEST_FILE);
        $this->assertSame($expectedTranslation, $parser->getTranslated($section.'.'.$key));
    }

    public function testReturnsEmptyStringIfUnknownSectionAndOrKey()
    {
        $parser = new LanguageFileParser(self::TEST_FILE);
        $this->assertSame('', $parser->getTranslated('SECUREPIN', 'LABEL_NEW_SECUREPIN'));
        $this->assertSame('', $parser->getTranslated('SECUREPIN.LABEL_NEW_SECUREPIN'));
    }

    public function testThrowsExceptionIfFileDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $parser = new LanguageFileParser(self::TEST_FILE.'foobar');
    }

    public function validTranslations()
    {
        return [
          ['ADMIN', 'WIDGETLABEL', 'Administrator'],
          ['ADMIN', 'LABEL_NEW_SECUREPIN', 'New SecurePIN:'],
          ['SECUREPIN', 'SUCCESS', 'You have entered the correct SecurePIN.'],
        ];
    }
}
