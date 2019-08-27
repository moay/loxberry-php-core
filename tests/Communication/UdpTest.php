<?php

namespace LoxBerry\Tests\Communication\ValueCache;

use LoxBerry\Communication\Http;
use LoxBerry\Communication\HttpResponse;
use LoxBerry\Communication\Udp;
use LoxBerry\Communication\ValueCache\UdpValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class UdpTest.
 */
class UdpTest extends TestCase
{
    public function testPushesUdpCallToMiniserver()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->once())
            ->method('sendToSocket')
            ->with($this->anything(), $this->callback(function ($buf) {
                return 'test=123 test2=234' === $buf;
            }));
        $httpMock = $this->createMock(Http::class);

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->push($miniserver, ['test' => 123, 'test2' => '234']);
    }

    public function testSplitsLongMessagesIntoSeveralMessages()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->exactly(2))
            ->method('sendToSocket');

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $httpMock = $this->createMock(Http::class);

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->push($miniserver, ['test' => str_repeat('test', 54), 'test2' => '234']);
    }

    public function testPrefixesLines()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->once())
            ->method('sendToSocket')
            ->with($this->anything(), $this->callback(function ($buf) {
                return 'testPrefix: test=123 test2=234' === $buf;
            }));

        $httpMock = $this->createMock(Http::class);

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->push($miniserver, ['test' => 123, 'test2' => '234'], 'testPrefix');
    }

    public function testUdpPortMustBeSetFirst()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $httpMock = $this->createMock(Http::class);

        $this->expectException(\LogicException::class);

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->push($miniserver, ['test' => 123, 'test2' => '234']);
    }

    public function testSendsOnlyChangedValuesIfRequestedTo()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $cacheMock
            ->expects($this->at(0))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_FULL_PUSH')
            ->willReturn((time() - 3200));
        $cacheMock
            ->expects($this->at(1))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_REBOOT_CHECK')
            ->willReturn(time() - 100);
        $cacheMock
            ->expects($this->at(2))
            ->method('valueDiffersFromStored')
            ->willReturn(false);
        $cacheMock
            ->expects($this->at(3))
            ->method('valueDiffersFromStored')
            ->willReturn(true);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->once())
            ->method('sendToSocket')
            ->with($this->anything(), $this->callback(function ($buf) {
                return 'test2=234' === $buf;
            }));

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $httpMock = $this->createMock(Http::class);

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->pushChanged($miniserver, ['test' => 123, 'test2' => '234']);
    }

    public function testResendsAllValuesEveryHour()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $cacheMock->method('valueDiffersFromStored')
            ->willReturn(false);
        $cacheMock
            ->method('put')
            ->willReturn(true);
        $cacheMock
            ->expects($this->at(0))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_FULL_PUSH')
            ->willReturn(time() - 4200);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->once())
            ->method('sendToSocket')
            ->with($this->anything(), $this->callback(function ($buf) {
                return 'test=123 test2=234' === $buf;
            }));

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $httpMock = $this->createMock(Http::class);

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->pushChanged($miniserver, ['test' => 123, 'test2' => '234']);
    }

    public function testResendsAllValuesAfterMiniserverReboot()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $cacheMock->method('valueDiffersFromStored')
            ->willReturn(false);
        $cacheMock
            ->method('put')
            ->willReturn(true);
        $cacheMock
            ->expects($this->at(0))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_FULL_PUSH')
            ->willReturn(time() - 200);
        $cacheMock
            ->expects($this->at(1))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_REBOOT_CHECK')
            ->willReturn(time() - 500);
        $cacheMock
            ->expects($this->at(3))
            ->method('get')
            ->with('COMMUNICATION_CACHINGTXP')
            ->willReturn(200);

        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->once())
            ->method('sendToSocket')
            ->with($this->anything(), $this->callback(function ($buf) {
                return 'test=123 test2=234' === $buf;
            }));

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $httpMock = $this->createMock(Http::class);
        $httpMock
            ->expects($this->once())
            ->method('call')
            ->with($miniserver, '/dev/lan/txp')
            ->willReturn(new HttpResponse('1', 200, 'test'));

        $udp = new Udp($cacheMock, $lowLevelMock, $httpMock);
        $udp->setUdpPort(9000)->pushChanged($miniserver, ['test' => 123, 'test2' => '234']);
    }
}
