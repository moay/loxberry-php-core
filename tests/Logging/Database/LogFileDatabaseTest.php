<?php

namespace LoxBerry\Tests\Logging\Database;

use LoxBerry\Exceptions\LogFileDatabaseException;
use LoxBerry\Logging\Database\LogFileDatabase;
use LoxBerry\Tests\Helpers\RetryTrait;
use Medoo\Medoo;
use PHPUnit\Framework\TestCase;

/**
 * Class LogFileDatabaseTest.
 */
class LogFileDatabaseTest extends TestCase
{
    use RetryTrait;

    const TEST_DB_FILE = __DIR__.'/test.dat';

    /** @var Medoo|\PHPUnit\Framework\MockObject\MockObject */
    private $databaseMock;

    protected function setUp(): void
    {
        $this->removeTestDbFile();
    }

    protected function tearDown(): void
    {
        $this->removeTestDbFile();
    }

    /**
     * @retry 5
     */
    public function testDatabaseIsInitializedProperly()
    {
        $this->setupDatabaseMock(['query', 'create']);

        $this->databaseMock->expects($this->at(0))
            ->method('query')
            ->with('PRAGMA journal_mode = wal;')
            ->willReturn(true);
        $this->databaseMock->expects($this->at(1))
            ->method('create')
            ->with('logs', [
                'LOGKEY' => ['INTEGER', 'NOT NULL', 'PRIMARY KEY'],
                'PACKAGE' => ['VARCHAR(255)', 'NOT NULL'],
                'NAME' => ['VARCHAR(255)', 'NOT NULL'],
                'FILENAME' => ['VARCHAR(255)', 'NOT NULL'],
                'LOGSTART' => ['DATETIME'],
                'LOGEND' => ['DATETIME'],
                'LASTMODIFIED' => ['DATETIME', 'NOT NULL'],
            ])
            ->willReturn(true);
        $this->databaseMock->expects($this->at(2))
            ->method('create')
            ->with('logs_attr', [
                'keyref' => ['INT', 'NOT NULL'],
                'attrib' => ['VARCHAR(255)', 'NOT NULL'],
                'value' => ['VARCHAR(255)'],
                'PRIMARY KEY (<keyref>, <attrib>)',
            ])
            ->willReturn(true);

        $database = new LogFileDatabase($this->databaseMock);
    }

    public function testGetsUnclosedSessionsProperly()
    {
        $this->setupDatabaseMock(['select']);
        $this->databaseMock->expects($this->once())
            ->method('select')
            ->with('logs', ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'], ['LOGKEY' => 'test', 'LOGEND[!]' => null])
            ->willReturn([[
                'PACKAGE' => 'test',
                'NAME' => 'test',
                'FILENAME' => 'test',
                'LOGSTART' => '2000-01-01 00:00:00',
            ]]);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $session = $logFileDatabase->getUnclosedLogSessionByKey('test');
        $this->assertEquals('test', $session['FILENAME']);
        $this->assertEquals('2000-01-01 00:00:00', $session['LOGSTART']);
    }

    public function testThrowsExceptionIfSessionWasNotRecreatable()
    {
        $this->setupDatabaseMock(['select']);
        $this->databaseMock->expects($this->once())
            ->method('select')
            ->with('logs', ['PACKAGE', 'NAME', 'FILENAME', 'LOGSTART'], ['LOGKEY' => 'test', 'LOGEND[!]' => null])
            ->willReturn([]);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $this->expectException(LogFileDatabaseException::class);
        $logFileDatabase->getUnclosedLogSessionByKey('test');
    }

    public function testReturnsAllLogAttributesProperly()
    {
        $this->setupDatabaseMock(['select']);
        $this->databaseMock->expects($this->once())
            ->method('select')
            ->with('logs_attr', ['attrib', 'value'], ['keyref' => 'test'])
            ->willReturn([['attrib' => 'test1', 'value' => 'testy'], ['attrib' => 'test2', 'value' => 'testy2']]);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $this->assertEquals(['test1' => 'testy', 'test2' => 'testy2'], $logFileDatabase->getAllAttributes('test'));
    }

    public function testReturnsASingleLogAttributeProperly()
    {
        $this->setupDatabaseMock(['select']);
        $this->databaseMock->expects($this->once())
            ->method('select')
            ->with('logs_attr', 'value', ['keyref' => 'test', 'attrib' => 'test2'])
            ->willReturn(['testy2']);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $this->assertEquals('testy2', $logFileDatabase->getAttribute('test', 'test2'));
    }

    public function testReturnsNullForNonExistantAttributes()
    {
        $this->setupDatabaseMock(['select']);
        $this->databaseMock->expects($this->once())
            ->method('select')
            ->with('logs_attr', 'value', ['keyref' => 'test', 'attrib' => 'test2'])
            ->willReturn([]);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $this->assertEquals(null, $logFileDatabase->getAttribute('test', 'test2'));
    }

    public function testAttributesAreInsertedProperly()
    {
        $this->setupDatabaseMock(['query']);
        $this->databaseMock->expects($this->at(1))
            ->method('query')
            ->with('INSERT OR REPLACE INTO <logs_attr> (<keyref>, <attrib>, <value>) VALUES (:keyref, :attrib, :value)', [
                ':keyref' => 'test',
                ':attrib' => 'testAttrib',
                ':value' => 'testValue',
            ]);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);
        $logFileDatabase->logAttribute('test', 'testAttrib', 'testValue');
    }

    public function testWritingAndReadingActuallyWork()
    {
        $this->setupDatabaseMock();
        $logFileDatabase = new LogFileDatabase($this->databaseMock);
        $logFileDatabase->logAttribute('test', 'testAttrib', 'testValue123');
        $this->assertEquals('testValue123', $logFileDatabase->getAttribute('test', 'testAttrib'));
    }

    /**
     * @retry 5
     */
    public function testLogStartIsWrittenProperly()
    {
        $now = new \DateTime();

        $this->setupDatabaseMock(['insert', 'id']);
        $this->databaseMock->expects($this->once())
            ->method('insert')
            ->with('logs', [
                'PACKAGE' => 'test',
                'NAME' => 'testName',
                'FILENAME' => 'testFile',
                'LOGSTART' => $now->format('Y-m-d H:i:s'),
                'LASTMODIFIED' => $now->format('Y-m-d H:i:s'),
            ]);
        $this->databaseMock->expects($this->once())
            ->method('id')
            ->willReturn(123);

        $logFileDatabase = new LogFileDatabase($this->databaseMock);
        $this->assertEquals(123, $logFileDatabase->logStart('test', 'testName', 'testFile', $now));
    }

    /**
     * @retry 5
     */
    public function testLogStartUsesCurrentDateAsDefaultIfNotProvided()
    {
        $now = new \DateTime();
        $this->setupDatabaseMock();
        $logFileDatabase = new LogFileDatabase($this->databaseMock);
        $id = $logFileDatabase->logStart('test', 'testName', 'testFile');

        $this->assertIsInt($id);
        $databaseRecord = $this->databaseMock->select('logs', ['LOGSTART'], ['LOGKEY' => $id])[0];
        $this->assertIsArray($databaseRecord);
        $this->assertEquals($now->format('Y-m-d H:i:s'), $databaseRecord['LOGSTART']);
    }

    /**
     * @retry 5
     */
    public function testLogEndChangesExistingRecordProperly()
    {
        $now = new \DateTime();
        $this->setupDatabaseMock(['select', 'update']);
        $this->databaseMock->method('select')
            ->willReturn([['test']]);
        $this->databaseMock->expects($this->once())
            ->method('update')
            ->with('logs', [
                'LASTMODIFIED' => $now->format('Y-m-d H:i:s'),
                'LOGEND' => $now->format('Y-m-d H:i:s'),
            ], [
                'LOGKEY' => 'test',
            ]);
        $logFileDatabase = new LogFileDatabase($this->databaseMock);
        $logFileDatabase->logEnd('test');
    }

    public function testLogEndWillThrowExceptionIfDatabaseRecordNotFound()
    {
        $this->setupDatabaseMock();
        $logFileDatabase = new LogFileDatabase($this->databaseMock);

        $this->expectException(LogFileDatabaseException::class);
        $this->expectExceptionMessage('Cannot find log session to close');
        $logFileDatabase->logEnd('something');
    }

    private function setupDatabaseMock($methods = [])
    {
        $this->databaseMock = $this->getMockBuilder(Medoo::class)
            ->onlyMethods($methods)
            ->setConstructorArgs([[
                'database_type' => 'sqlite',
                'database_file' => self::TEST_DB_FILE,
            ]])
            ->getMock();
    }

    private function removeTestDbFile()
    {
        if (file_exists(self::TEST_DB_FILE)) {
            unlink(self::TEST_DB_FILE);
        }
    }
}
