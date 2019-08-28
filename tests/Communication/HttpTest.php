<?php

namespace LoxBerry\Tests\Communication;

use LoxBerry\Communication\Http;
use LoxBerry\Communication\HttpResponse;
use LoxBerry\Communication\ValueCache\HttpValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpTest.
 */
class HttpTest extends TestCase
{
    public function testSetsLoxoneBlocksProperly()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock/Off')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="Off" Code="200"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->setBlockValue($miniserver, 'testBlock', 'Off', false);
        $this->assertTrue($response);
    }

    public function testTakesSeveralValuesAtOnce()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->at(0))
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock/Off')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="Off" Code="200"/>')
        ;
        $lowLevelMock->expects($this->at(1))
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock2/On')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="Off" Code="500"/>')
        ;
        $lowLevelMock->expects($this->at(2))
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock3/123')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="123" Code="200"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->setBlockValues($miniserver, [
            'testBlock' => 'Off',
            'testBlock2' => 'On',
            'testBlock3' => '123',
        ], false);
        $this->assertEquals([
            'testBlock' => true,
            'testBlock2' => false,
            'testBlock3' => true,
        ], $response);
    }

    public function testSetsLoxoneBlocksProperlyWhenUsingNumericValues()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock/1234')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="1234" Code="200"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->setBlockValue($miniserver, 'testBlock', 1234, false);
        $this->assertTrue($response);
    }

    public function testGetsLoxoneBlocksProperly()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="Off" Code="200"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->getBlockValue($miniserver, 'testBlock');
        $this->assertEquals('Off', $response);
    }

    public function testCallsCommandsProperly()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/someTestCommand')
            ->willReturn('<LL control="someTestCommand" value="1234" Code="200"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->call($miniserver, '/someTestCommand');
        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertEquals(1234, $response->getValue());
        $this->assertEquals(200, $response->getResponseCode());
    }

    public function testReactsToFaultyReponseCodeProperly()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $lowLevelMock->expects($this->once())
            ->method('fileGetContents')
            ->with('http://test1:test2@123.123.0.1:9755/dev/sps/io/testBlock/1234')
            ->willReturn('<LL control="dev/sps/io/testBlock" value="123" Code="500"/>')
        ;

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->setBlockValue($miniserver, 'testBlock', 1234, false);
        $this->assertFalse($response);
    }

    public function testSendsOnlyUncachedValuesWhenTryingToCache()
    {
        $valueCacheMock = $this->createMock(HttpValueCache::class);
        $lowLevelMock = $this->createMock(LowLevelExecutor::class);

        $valueCacheMock
            ->expects($this->at(0))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_FULL_PUSH')
            ->willReturn(time() - 2300);

        $valueCacheMock
            ->expects($this->at(1))
            ->method('get')
            ->with('COMMUNICATION_CACHINGLAST_REBOOT_CHECK')
            ->willReturn(time() - 100);

        $valueCacheMock->method('valueDiffersFromStored')->willReturn(false);

        $lowLevelMock->expects($this->never())
            ->method('fileGetContents');

        $miniserver = new MiniserverInformation();
        $miniserver->setName('test');
        $miniserver->setAdminUsername('test1');
        $miniserver->setAdminPassword('test2');
        $miniserver->setIpAddress('123.123.0.1');
        $miniserver->setPort(9755);

        $http = new Http($valueCacheMock, $lowLevelMock);
        $response = $http->setBlockValue($miniserver, 'testBlock', 1234);
        $this->assertTrue($response);
    }
}
