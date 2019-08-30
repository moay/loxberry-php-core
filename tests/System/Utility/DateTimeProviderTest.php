<?php

namespace LoxBerry\Tests\System\Utility;

use DateTime;
use DateTimeZone;
use LoxBerry\System\Utility\DateTimeProvider;
use LoxBerry\Tests\Helpers\RetryTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeProviderTest.
 */
class DateTimeProviderTest extends TestCase
{
    use RetryTrait;

    public function testUnixTimestampIsConvertedProperly()
    {
        $origin_tz = date_default_timezone_get();
        $origin_dtz = new DateTimeZone($origin_tz);
        $remote_dtz = new DateTimeZone('UTC');
        $origin_dt = new DateTime('now', $origin_dtz);
        $remote_dt = new DateTime('now', $remote_dtz);
        $offset_tz = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        $offset = 1230764400; // 1.1.2009 00:00:00
        $loxepoche = time() - $offset + $offset_tz - 3600;

        $provider = new DateTimeProvider();
        $this->assertEquals($loxepoche, $provider->getLoxBerryTimestamp());
    }

    public function testLoxBerryTimestampIsProvidedProperly()
    {
        $provider = new DateTimeProvider();
        $this->assertEquals(time(), $provider->getUnixTimestamp());
    }

    public function testLoxBerryTimestampIsConvertedProperly()
    {
        $origin_tz = date_default_timezone_get();
        $origin_dtz = new DateTimeZone($origin_tz);
        $remote_dtz = new DateTimeZone('UTC');
        $origin_dt = new DateTime('now', $origin_dtz);
        $remote_dt = new DateTime('now', $remote_dtz);
        $offset_tz = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        $offset = 1230764400; // 1.1.2009 00:00:00
        $epoche = time() + $offset - $offset_tz + 3600;
        $provider = new DateTimeProvider();
        $this->assertEquals($epoche, $provider->getUnixTimestamp(time()));
    }

    /**
     * @retry 25
     * @dataProvider dateFormattingExamples
     */
    public function testFormattedDateTimeProviderFormatsStringsCorrectly($format, $expectedFormat)
    {
        $provider = new DateTimeProvider();
        $this->assertEquals(date($expectedFormat), $provider->getCurrentDateTimeFormatted($format));
    }

    public function dateFormattingExamples()
    {
        return [
            [DateTimeProvider::DATE_TIME_FORMAT_HR, 'd.m.Y H:i:s'],
            [DateTimeProvider::DATE_TIME_FORMAT_HR_TIME, 'H:i:s'],
            [DateTimeProvider::DATE_TIME_FORMAT_FILE, 'Ymd_His'],
            [DateTimeProvider::DATE_TIME_FORMAT_ISO, '"Y-m-d\TH:i:sO"'],
        ];
    }
}
