<?php

namespace LoxBerry\Tests\Logging\Database;

use LoxBerry\Exceptions\LogFileDatabaseException;
use LoxBerry\Logging\Database\LogFileDatabase;
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

    public function testGetsUnclosedSessionsProperly()
    {
        $medooMock = $this->getMockBuilder(Medoo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $medooMock->expects($this->once())
            ->method('select')
            ->with('PACKAGE', ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'], ['LOGKEY' => 'test', 'LOGEND[!]' => null])
            ->willReturn([[
                'PACKAGE' => 'test',
                'NAME' => 'test',
                'FILENAME' => 'test',
                'LOGSTART' => '2000-01-01 00:00:00',
            ]]);

        $logFileDatabase = $this->getMockBuilder(LogFileDatabase::class)
            ->setConstructorArgs([$medooMock])
            ->onlyMethods(['initializeDatabase'])
            ->getMock();

        $reflection = new \ReflectionClass(LogFileDatabase::class);
        $reflection_property = $reflection->getProperty('database');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($logFileDatabase, $medooMock);

        $session = $logFileDatabase->getUnclosedLogSessionByKey('test');
        $this->assertEquals('test', $session['FILENAME']);
        $this->assertEquals('2000-01-01 00:00:00', $session['LOGSTART']);
    }

    public function testThrowsExceptionIfSessionWasNotRecreatable()
    {
        $medooMock = $this->getMockBuilder(Medoo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $medooMock->expects($this->once())
            ->method('select')
            ->with('PACKAGE', ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'], ['LOGKEY' => 'test', 'LOGEND[!]' => null])
            ->willReturn([]);

        $logFileDatabase = $this->getMockBuilder(LogFileDatabase::class)
            ->setConstructorArgs([$medooMock])
            ->onlyMethods(['initializeDatabase'])
            ->getMock();

        $reflection = new \ReflectionClass(LogFileDatabase::class);
        $reflection_property = $reflection->getProperty('database');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($logFileDatabase, $medooMock);

        $this->expectException(LogFileDatabaseException::class);
        $logFileDatabase->getUnclosedLogSessionByKey('test');
    }
}
