<?php

namespace LoxBerry\Tests\Communication;

use LoxBerry\Communication\Notifications;
use LoxBerry\System\Plugin\PluginDatabase;
use LoxBerry\System\Utility\HttpAccess\AjaxNotificationHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class NotificationsTest.
 */
class NotificationsTest extends TestCase
{
    public function testRetrievesNotificationsProperly()
    {
        $ajaxNotificationHandlerMock = $this->createMock(AjaxNotificationHandler::class);
        $ajaxNotificationHandlerMock->expects($this->once())
            ->method('getAsArray')
            ->with(['action' => 'get_notifications', 'package' => 'testPackage', 'name' => 'testName'])
            ->willReturn(['test1', 'test2']);

        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $notifications = new Notifications($ajaxNotificationHandlerMock, $pluginDatabaseMock);
        $this->assertEquals(['test1', 'test2'], $notifications->get('testPackage', 'testName'));
    }

    public function testRetrievesNotificationsHtmlProperly()
    {
        $ajaxNotificationHandlerMock = $this->createMock(AjaxNotificationHandler::class);
        $ajaxNotificationHandlerMock->expects($this->once())
            ->method('getAsString')
            ->with(['action' => 'get_notifications_html', 'package' => 'testPackage', 'name' => 'testName', 'type' => 'all'])
            ->willReturn('test');

        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $notifications = new Notifications($ajaxNotificationHandlerMock, $pluginDatabaseMock);
        $this->assertEquals('test', $notifications->getHtml('testPackage', 'testName'));
    }

    public function testThrowsErrorIfNotificationTypeIsNotCorrect()
    {
        $ajaxNotificationHandlerMock = $this->createMock(AjaxNotificationHandler::class);

        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);

        $notifications = new Notifications($ajaxNotificationHandlerMock, $pluginDatabaseMock);
        $this->expectException(\InvalidArgumentException::class);
        $notifications->getHtml('testPackage', 'testName', 'test');
    }

    public function testPushesSimpleNotification()
    {
        $ajaxNotificationHandlerMock = $this->createMock(AjaxNotificationHandler::class);
        $ajaxNotificationHandlerMock->expects($this->once())
            ->method('execute')
            ->with([
                'action' => 'notifyext',
                'package' => 'testPackage',
                'name' => 'testName',
                '_ISPLUGIN' => 1,
                'message' => 'testMessage',
                'severity' => 3,
            ])
            ->willReturn('test');

        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);
        $pluginDatabaseMock->expects($this->once())
            ->method('isInstalledPlugin')
            ->with('testPackage')
            ->willReturn(true);

        $notifications = new Notifications($ajaxNotificationHandlerMock, $pluginDatabaseMock);
        $notifications->notify('testPackage', 'testName', 'testMessage');
    }

    public function testPushesExtendedNotification()
    {
        $ajaxNotificationHandlerMock = $this->createMock(AjaxNotificationHandler::class);
        $ajaxNotificationHandlerMock->expects($this->once())
            ->method('execute')
            ->with([
                'action' => 'notifyext',
                'package' => 'testPackage',
                'name' => 'testName',
                '_ISSYSTEM' => 1,
                'message' => 'testMessage',
                'severity' => 6,
                'additional' => 'data',
                'additional2' => 'data2',
            ])
            ->willReturn('test');

        $pluginDatabaseMock = $this->createMock(PluginDatabase::class);
        $pluginDatabaseMock->expects($this->once())
            ->method('isInstalledPlugin')
            ->with('testPackage')
            ->willReturn(false);

        $notifications = new Notifications($ajaxNotificationHandlerMock, $pluginDatabaseMock);
        $notifications->notify('testPackage', 'testName', 'testMessage', ['additional' => 'data', 'additional2' => 'data2'], true);
    }
}
