<?php

namespace LoxBerry\Tests\Logging;

use LoxBerry\Logging\LogFileDatabase;
use Medoo\Medoo;
use PHPUnit\Framework\TestCase;

/**
 * Class LogFileDatabaseTest.
 */
class LogFileDatabaseTest extends TestCase
{
    public function testDatabaseIsInitializedProperly()
    {
        $medooMock = $this->getMockBuilder(Medoo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $medooMock->expects($this->at(0))
            ->method('query')
            ->with('PRAGMA journal_mode = wal;')
            ->willReturn(true);
        $medooMock->expects($this->at(1))
            ->method('create')
            ->with('logs', [
                'LOGKEY' => ['INT', 'NOT NULL', 'PRIMARY KEY'],
                'PACKAGE' => ['VARCHAR(255)', 'NOT NULL'],
                'NAME' => ['VARCHAR(255)', 'NOT NULL'],
                'FILENAME' => ['VARCHAR(255)', 'NOT NULL'],
                'LOGSTART' => ['DATETIME'],
                'LOGEND' => ['DATETIME'],
                'LASTMODIFIED' => ['DATETIME', 'NOT NULL'],
            ])
            ->willReturn(true);
        $medooMock->expects($this->at(2))
            ->method('create')
            ->with('logs_attr', [
                'keyref' => ['INT', 'NOT NULL'],
                'attrib' => ['VARCHAR(255)', 'NOT NULL'],
                'value' => ['VARCHAR(255)'],
                'PRIMARY KEY (<keyref>, <attrib>)',
            ])
            ->willReturn(true);

        $database = new LogFileDatabase($medooMock);
    }
}
