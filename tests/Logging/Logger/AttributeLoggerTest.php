<?php

namespace LoxBerry\Tests\Logging\Logger;

use LoxBerry\Logging\Database\LogFileDatabase;
use LoxBerry\Logging\Logger\AttributeLogger;
use PHPUnit\Framework\TestCase;

/**
 * Class AttributeLoggerTest.
 */
class AttributeLoggerTest extends TestCase
{
    public function testAttributeIsLoggedProperly()
    {
        $databaseMock = $this->createMock(LogFileDatabase::class);
        $databaseMock->expects($this->once())
            ->method('logAttribute')
            ->with('test', 'testKey', 'testValue');
        $logger = new AttributeLogger($databaseMock);
        $logger->logAttribute('test', 'testKey', 'testValue');
    }
}
