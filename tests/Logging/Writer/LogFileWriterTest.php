<?php

namespace LoxBerry\Tests\Logging\Writer;

use LoxBerry\Logging\Event\LogEvent;
use LoxBerry\Logging\Logger;
use LoxBerry\Logging\Writer\LogFileWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class LogFileWriterTest.
 */
class LogFileWriterTest extends TestCase
{
    const TEST_FILE = 'testlogfile.log';

    public function testLogFileIsCreated()
    {
        $this->assertFileNotExists(__DIR__.'/'.self::TEST_FILE);
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->initialize();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
    }

    public function testExistingLogfileIsOverwritten()
    {
        file_put_contents(__DIR__.'/'.self::TEST_FILE, 'This is a test');
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('test', file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->initialize();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringNotContainsString('test', file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testExistingLogfileWillBeExtendedIfSetTo()
    {
        file_put_contents(__DIR__.'/'.self::TEST_FILE, 'This is a test');
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('test', file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE, false);
        $logWriter->initialize();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('test', file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testMessageIsWrittenProperlyToOwnLine()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logEvent($logEvent);
        $time = new \DateTime();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('<ERROR> testerror (testfile, L22)'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString($time->format('Y-m-d H:i'), file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testSecondLogWillBeWrittenInOwnLine()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);
        $logEvent2 = new LogEvent('testerror2', Logger::LOGLEVEL_CRITICAL_ERROR, 'testfile2', 23);

        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logEvent($logEvent);
        $logWriter->logEvent($logEvent2);
        $time = new \DateTime();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('<ERROR> testerror (testfile, L22)'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString($time->format('Y-m-d H:i'), file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString('<CRITICAL> testerror2 (testfile2, L23)'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testMessageIsWrittenWithoutLineNumberAndFile()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR);

        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logEvent($logEvent);
        $time = new \DateTime();
        $this->assertFileExists(__DIR__.'/'.self::TEST_FILE);
        $this->assertStringContainsString('<ERROR> testerror'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString($time->format('Y-m-d H:i'), file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testExistingLogWillNotBeOverwrittenWhenAppending()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        file_put_contents(__DIR__.'/'.self::TEST_FILE, 'This is a test'.PHP_EOL);
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE, false);
        $logWriter->logEvent($logEvent);
        $this->assertStringContainsString('This is a test'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testExistingLogWillBeOverwrittenWhenNotAppending()
    {
        $logEvent = new LogEvent('testerror', Logger::LOGLEVEL_ERROR, 'testfile', 22);

        file_put_contents(__DIR__.'/'.self::TEST_FILE, 'This is a test'.PHP_EOL);
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logEvent($logEvent);
        $this->assertStringNotContainsString('This is a test'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testLogStartIsWrittenProperly()
    {
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logStart();
        $this->assertStringContainsString('================================================================================'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $date = date('Y-m-d H:i:');
        $this->assertStringContainsString($date, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString('<LOGSTART> TASK STARTED'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $logWriter->logEnd();
    }

    public function testLogEndIsWrittenProperly()
    {
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logStart();
        $logWriter->logEnd();
        $date = date('Y-m-d H:i:');
        $this->assertStringContainsString($date, file_get_contents(__DIR__.'/'.self::TEST_FILE));
        $this->assertStringContainsString('<LOGEND> TASK FINISHED'.PHP_EOL, file_get_contents(__DIR__.'/'.self::TEST_FILE));
    }

    public function testLogEndWontWriteAnythingIfNotStarted()
    {
        $logWriter = new LogFileWriter(__DIR__.DIRECTORY_SEPARATOR.self::TEST_FILE);
        $logWriter->logEnd();
        $date = date('Y-m-d H:i:');
        $this->assertFileNotExists(__DIR__.'/'.self::TEST_FILE);
    }

    protected function setUp(): void
    {
        $this->removeTestFile();
    }

    protected function tearDown(): void
    {
        $this->removeTestFile();
    }

    private function removeTestFile()
    {
        if (file_exists(__DIR__.'/'.self::TEST_FILE)) {
            unlink(__DIR__.'/'.self::TEST_FILE);
        }
    }
}
