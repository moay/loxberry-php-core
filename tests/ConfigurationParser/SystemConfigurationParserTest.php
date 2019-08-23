<?php

namespace LoxBerry\Tests\ConfigurationParser;

use LoxBerry\ConfigurationParser\SystemConfigurationParser;
use LoxBerry\System\PathProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class SystemConfigurationParserTest.
 */
class SystemConfigurationParserTest extends TestCase
{
    const TEST_FILE = __DIR__.'/resources/general.cfg';
    const TEST_FILE_FOR_EDITING = __DIR__.'/resources/general.cfg_copy';

    /**
     * @dataProvider regularValues
     */
    public function testProvidesRegularValues($section, $key, $expectedValue)
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_FILE);

        $parser = new SystemConfigurationParser($pathProviderMock);
        $this->assertEquals($expectedValue, $parser->get($section, $key));
        $this->assertTrue($parser->has($section, $key));
    }

    public function regularValues()
    {
        return [
            ['TIMESERVER', 'ZONE', 'Europe/Berlin'],
            ['TIMESERVER', 'SERVER', '0.europe.pool.ntp.org'],
            ['TIMESERVER', 'METHOD', 'ntp'],
            ['WEBSERVER', 'PORT', 80],
            ['NETWORK', 'INTERFACE', 'eth0'],
            ['NETWORK', 'DNS', null],
            ['NETWORK', 'MASK', null],
            ['NETWORK', 'GATEWAY', null],
            ['NETWORK', 'FRIENDLYNAME', 'Testname'],
            ['NETWORK', 'IPADDRESS', null],
            ['NETWORK', 'TYPE', 'dhcp'],
            ['NETWORK', 'SSID', null],
            ['BASE', 'STARTSETUP', 1],
            ['BASE', 'INSTALLFOLDER', '/opt/loxberry'],
            ['BASE', 'MINISERVERS', 1],
            ['BASE', 'LANG', 'de'],
            ['BASE', 'VERSION', '1.5.0.4'],
            ['BASE', 'CLOUDDNS', 'dns.loxonecloud.com'],
            ['BASE', 'SYSTEMLOGLEVEL', 6],
            ['BASE', 'SENDSTATISTIC', 1],
            ['BINARIES', 'SUDO', '/usr/bin/sudo'],
            ['BINARIES', 'BASH', '/bin/bash'],
            ['BINARIES', 'UNZIP', '/usr/bin/unzip'],
            ['BINARIES', 'CHOWN', '/bin/chown'],
            ['BINARIES', 'CHMOD', '/bin/chmod'],
            ['BINARIES', 'GREP', '/bin/grep'],
            ['BINARIES', 'DATE', '/bin/date'],
            ['BINARIES', 'DPKG', '/usr/bin/dpkg'],
            ['BINARIES', 'ZIP', '/usr/bin/zip'],
            ['BINARIES', 'CURL', '/usr/bin/curl'],
            ['BINARIES', 'GZIP', '/bin/gzip'],
            ['BINARIES', 'APT', '/usr/bin/apt-get'],
            ['BINARIES', 'FIND', '/usr/bin/find'],
            ['BINARIES', 'TAR', '/bin/tar'],
            ['BINARIES', 'SENDMAIL', '/usr/sbin/sendmail'],
            ['BINARIES', 'BZIP2', '/bin/bzip2'],
            ['BINARIES', 'WGET', '/usr/bin/wget'],
            ['BINARIES', 'MAIL', '/usr/bin/mailx'],
            ['BINARIES', 'POWEROFF', '/sbin/poweroff'],
            ['BINARIES', 'DOS2UNIX', '/usr/bin/dos2unix'],
            ['BINARIES', 'REBOOT', '/sbin/reboot'],
            ['BINARIES', 'NTPDATE', '/usr/sbin/ntpdate'],
            ['BINARIES', 'AWK', '/usr/bin/awk'],
            ['MINISERVER1', 'PASS', 'test12345'],
            ['MINISERVER1', 'IPADDRESS', '192.168.0.0'],
            ['MINISERVER1', 'ADMIN', 'Admin'],
            ['MINISERVER1', 'ENCRYPTRESPONSE', null],
            ['MINISERVER1', 'PORT', '80'],
            ['MINISERVER1', 'CLOUDURL', null],
            ['MINISERVER1', 'USECLOUDDNS', 0],
            ['MINISERVER1', 'SECUREGATEWAY', null],
            ['MINISERVER1', 'CLOUDURLFTPPORT', null],
            ['MINISERVER1', 'NAME', 'Miniserver'],
            ['MINISERVER1', 'NOTE', null],
            ['UPDATE', 'INTERVAL', 7],
            ['UPDATE', 'LATESTSHA', '81fe798f726cbd6150aac2828bb3e0e0359a8d13'],
            ['UPDATE', 'BRANCH', 'master'],
            ['UPDATE', 'RELEASETYPE', 'release'],
            ['UPDATE', 'INSTALLTYPE', 'notify'],
            ['SSDP', 'UUID', '488C8D4F-CB62-415D-B694-8A85847CCC3C'],
        ];
    }

    /**
     * @dataProvider shortcutMethods
     */
    public function testHasShortcutMethodsForImportantValues($methodName, $expectedValue)
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_FILE);

        $parser = new SystemConfigurationParser($pathProviderMock);
        $this->assertEquals($expectedValue, $parser->{$methodName}());
    }

    public function shortcutMethods()
    {
        return [
            ['getLoxBerryVersion', '1.5.0.4'],
            ['getNetworkName', 'Testname'],
            ['getLanguage', 'de'],
            ['getBinaries', [
                'SUDO' => '/usr/bin/sudo',
                'BASH' => '/bin/bash',
                'UNZIP' => '/usr/bin/unzip',
                'CHOWN' => '/bin/chown',
                'CHMOD' => '/bin/chmod',
                'GREP' => '/bin/grep',
                'DATE' => '/bin/date',
                'DPKG' => '/usr/bin/dpkg',
                'ZIP' => '/usr/bin/zip',
                'CURL' => '/usr/bin/curl',
                'GZIP' => '/bin/gzip',
                'APT' => '/usr/bin/apt-get',
                'FIND' => '/usr/bin/find',
                'TAR' => '/bin/tar',
                'SENDMAIL' => '/usr/sbin/sendmail',
                'BZIP2' => '/bin/bzip2',
                'WGET' => '/usr/bin/wget',
                'MAIL' => '/usr/bin/mailx',
                'POWEROFF' => '/sbin/poweroff',
                'DOS2UNIX' => '/usr/bin/dos2unix',
                'REBOOT' => '/sbin/reboot',
                'NTPDATE' => '/usr/sbin/ntpdate',
                'AWK' => '/usr/bin/awk',
            ]],
            ['getNumberOfMiniservers', 1],
            ['getCloudDnsAddress', 'dns.loxonecloud.com'],
            ['getWebserverPort', 80],
        ];
    }

    public function testReturnsNullForUnknownValues()
    {
        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_FILE);

        $parser = new SystemConfigurationParser($pathProviderMock);
        $this->assertNull($parser->get('something', 'unknown'));
        $this->assertFalse($parser->has('something', 'unknown'));
    }

    public function testWritesValuesProperly()
    {
        copy(self::TEST_FILE, self::TEST_FILE_FOR_EDITING);

        $pathProviderMock = $this->createMock(PathProvider::class);
        $pathProviderMock->expects($this->once())
            ->method('getPath')
            ->willReturn(self::TEST_FILE_FOR_EDITING);

        $parser = new SystemConfigurationParser($pathProviderMock);
        $this->assertNull($parser->get('testsection', 'testkey'));
        $parser->set('testsection', 'testkey', 'testvalue');
        $this->assertSame('testvalue', $parser->get('testsection', 'testkey'));
        $this->assertStringContainsString('testkey = testvalue', file_get_contents(self::TEST_FILE_FOR_EDITING));
        $this->assertStringContainsString('[testsection]', file_get_contents(self::TEST_FILE_FOR_EDITING));
    }

    public function testLoadsMiniserverInformation()
    {
        $this->markTestIncomplete();
    }

    protected function setUp(): void
    {
        if (file_exists(self::TEST_FILE_FOR_EDITING)) {
            unlink(self::TEST_FILE_FOR_EDITING);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::TEST_FILE_FOR_EDITING)) {
            unlink(self::TEST_FILE_FOR_EDITING);
        }
    }
}
