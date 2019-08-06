<?php

namespace LoxBerry\Tests\ConfigurationParser;

use LoxBerry\ConfigurationParser\ConfigurationParser;
use LoxBerry\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationParserTest.
 */
class ConfigurationParserTest extends TestCase
{
    const TEST_CONFIG_FILE = __DIR__ . '/example_configuration.cfg';

    protected function setUp(): void
    {
        file_put_contents(self::TEST_CONFIG_FILE,
            '[TEST1]' . "\n"
            . 'test = \'test\'' . "\n"
            . 'test2 = \'test2\'' . "\n"
            . "\n"
            . '[TEST3]' . "\n"
            . 'test4 = \'test4\'' . "\n"
        );
    }

    public function testConfigurationIsReadFromFile()
    {
        $configuration = new ConfigurationParser(self::TEST_CONFIG_FILE);
        $this->assertEquals('test', $configuration->get('TEST1', 'test'));
        $this->assertEquals('test4', $configuration->get('TEST3', 'test4'));
    }

    public function testConfigurationThrowsExceptionIfFileDoesNotExist()
    {
        $this->expectException(ConfigurationException::class);
        $configuration = new ConfigurationParser('nofile.txt');
    }

    public function testDefaultValueGetsReturnedIfProvided()
    {
        $configuration = new ConfigurationParser(self::TEST_CONFIG_FILE);
        $this->assertEquals('fallback', $configuration->get('BLING', 'blingKey', 'fallback'));
    }

    public function testValueCanBeSet()
    {
        $configuration = new ConfigurationParser(self::TEST_CONFIG_FILE);
        $configuration->set('BLING', 'blingKey', 'blingValue');
        $this->assertEquals('blingValue', $configuration->get('BLING', 'blingKey'));
    }

    public function testChangesGetSavedToFile()
    {
        $this->assertStringNotContainsString('blingKey = "blingValue"', file_get_contents(self::TEST_CONFIG_FILE));
        $configuration = new ConfigurationParser(self::TEST_CONFIG_FILE);
        $configuration->set('BLING', 'blingKey', 'blingValue');
        $this->assertStringContainsString('blingKey = "blingValue"', file_get_contents(self::TEST_CONFIG_FILE));
    }

    public function testCanOutputInformationOnWhetherOrNotKeysExist()
    {
        $configuration = new ConfigurationParser(self::TEST_CONFIG_FILE);
        $this->assertFalse($configuration->has('UNKNOWN', 'unknown'));
        $this->assertFalse($configuration->has('UNKNOWN'));
        $this->assertTrue($configuration->has('TEST3', 'test4'));
        $this->assertTrue($configuration->has('TEST3'));
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::TEST_CONFIG_FILE);
    }

}
