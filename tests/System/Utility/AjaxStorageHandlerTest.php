<?php

namespace LoxBerry\Tests\System\Utility;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\LowLevelExecutor;
use LoxBerry\System\Utility\HttpAccess\AjaxStorageHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class AjaxStorageHandlerTest.
 */
class AjaxStorageHandlerTest extends TestCase
{
    public function testGetsStorageDevices()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-storage-handler.cgi')
            ->willReturn('<div class="test">');

        $ajaxStorageHandler = new AjaxStorageHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals('<div class="test">', $ajaxStorageHandler->getStorageDeviceSelect('testId'));
    }

    public function testChecksConfigurationForValidAttributes()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $ajaxStorageHandler = new AjaxStorageHandler($systemConfigurationMock, $lowLevelMock);

        $this->expectException(\InvalidArgumentException::class);
        $ajaxStorageHandler->getStorageDeviceSelect('testId', ['unknownFlag']);
    }

    public function testAllowsKnownFlags()
    {
        $systemConfigurationMock = $this->createMock(SystemConfigurationParser::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $systemConfigurationMock->expects($this->once())
            ->method('getWebserverPort')
            ->willReturn(9999);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://localhost:9999/admin/system/tools/ajax-storage-handler.cgi')
            ->willReturn('<div class="test">');

        $ajaxStorageHandler = new AjaxStorageHandler($systemConfigurationMock, $lowLevelMock);
        $this->assertEquals('<div class="test">', $ajaxStorageHandler->getStorageDeviceSelect('testId', ['type_local', 'type_custom', 'data_mini', 'label' => 'test']));
    }

    public function testPreparesFlagsAndConfigurationsProperly()
    {
        $ajaxStorageHandler = $this->createPartialMock(AjaxStorageHandler::class, ['post']);
        $ajaxStorageHandler->expects($this->once())
            ->method('post')
            ->with([
                'action' => 'init',
                'formid' => 'testId',
                'type_local' => 1,
                'type_custom' => 1,
                'data_mini' => 1,
                'label' => 'test',
            ])
            ->willReturn('test');

        $response = $ajaxStorageHandler->getStorageDeviceSelect('testId', [
            AjaxStorageHandler::FLAG_TYPE_LOCAL,
            AjaxStorageHandler::FLAG_TYPE_CUSTOM,
            AjaxStorageHandler::FLAG_MINI,
            AjaxStorageHandler::CONFIGURATION_LABEL => 'test',
        ]);
        $this->assertEquals('test', $response);
    }
}
