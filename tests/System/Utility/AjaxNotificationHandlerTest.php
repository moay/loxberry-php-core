<?php

namespace LoxBerry\Tests\System\Utility;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\Utility\HttpAccess\AjaxNotificationHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class AjaxNotificationHandlerTest.
 */
class AjaxNotificationHandlerTest extends TestCase
{
    public function testGetsDataAsString()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn('test');

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals('test', $handler->getAsString(['test1' => 'test2']));
    }

    public function testReturnsFalseOnRequestAsStringIfFalsyResponse()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(80);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:80/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn(false);

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(false, $handler->getAsString(['test1' => 'test2']));
    }

    public function testGetsDataAsArrayExpectingJson()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn('{"test": "test123"}');

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(['test' => 'test123'], $handler->getAsArray(['test1' => 'test2']));
    }

    public function testReturnsNullIfFaultyJsonReturnedOnArrayRequest()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn('{"test: "test123}');

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(null, $handler->getAsArray(['test1' => 'test2']));
    }

    public function testReturnsNullIfNothingReturnedOnArrayRequest()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn(false);

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(null, $handler->getAsArray(['test1' => 'test2']));
    }

    public function testFalseIfExecutionRequestFails()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn(false);

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(false, $handler->execute(['test1' => 'test2']));
    }

    public function testTrueIfExecutionRequestSucceeds()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-notification-handler.cgi')
            ->willReturn('success');

        $handler = new AjaxNotificationHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals(true, $handler->execute(['test1' => 'test2']));
    }
}
