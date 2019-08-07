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
        $medooMock->expects($this->once())
            ->method('query')
            ->with('PRAGMA journal_mode = wal;')
            ->willReturn(true);

        $database = new LogFileDatabase($medooMock);
    }
}
