<?php

namespace LoxBerry\Tests\System\Utility;

use LoxBerry\Communication\Http;
use LoxBerry\Communication\HttpResponse;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\Utility\FtpPortDeterminator;
use PHPUnit\Framework\TestCase;

/**
 * Class FtpPortDeterminatorTest.
 */
class FtpPortDeterminatorTest extends TestCase
{
    public function testDeterminesMiniserverFtpPortFromMiniserverConfigurationIfPresent()
    {
        $httpMock = $this->createMock(Http::class);

        $miniserver = new MiniserverInformation();
        $miniserver->setCloudUrlFftPort(123);
        $miniserver->setUseCloudDns(true);

        $determinator = new FtpPortDeterminator($httpMock);
        $this->assertEquals(123, $determinator->getFtpPort($miniserver));
    }

    public function testDeterminesMiniserverFtpPortFromMiniserverIfNotProvided()
    {
        $httpMock = $this->createMock(Http::class);

        $miniserver = new MiniserverInformation();
        $miniserver->setUseCloudDns(false);

        $response = new HttpResponse(234, 200);

        $httpMock->expects($this->once())
            ->method('call')
            ->with($miniserver, '/dev/cfg/ftp')
            ->willReturn($response);

        $determinator = new FtpPortDeterminator($httpMock);
        $this->assertEquals(234, $determinator->getFtpPort($miniserver));
    }
}
