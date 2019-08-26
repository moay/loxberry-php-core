<?php

namespace LoxBerry\Tests\Communication\Udp;

use LoxBerry\Communication\Udp\Udp;
use LoxBerry\Communication\Udp\UdpValueCache;
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

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $udp = new Udp($cacheMock, $lowLevelMock);
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

        $udp = new Udp($cacheMock, $lowLevelMock);
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

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $udp = new Udp($cacheMock, $lowLevelMock);
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

        $this->expectException(\LogicException::class);

        $udp = new Udp($cacheMock, $lowLevelMock);
        $udp->push($miniserver, ['test' => 123, 'test2' => '234']);
    }

    public function testSendsOnlyChangedValuesIfRequestedTo()
    {
        $cacheMock = $this->createMock(UdpValueCache::class);
        $cacheMock->method('valueDiffersFromStored')
            ->willReturn(false);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);
        $lowLevelMock->expects($this->never())
            ->method('sendToSocket');

        $miniserver = new MiniserverInformation();
        $miniserver->setIpAddress('127.0.0.1');
        $miniserver->setPort(90);
        $miniserver->setName('Test');

        $udp = new Udp($cacheMock, $lowLevelMock);
        $udp->setUdpPort(9000)->pushChanged($miniserver, ['test' => 123, 'test2' => '234']);
    }
}
