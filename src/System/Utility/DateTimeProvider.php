<?php

namespace LoxBerry\System\Utility;

/**
 * Class DateTimeProvider.
 */
class DateTimeProvider
{
    /** Offset to 2009-01-01 00:00:00  */
    const LOXBERRY_TIME_OFFSET = 1230764400;

    const DATE_TIME_FORMAT_HR = 'hr';
    const DATE_TIME_FORMAT_HR_TIME = 'hrtime';
    const DATE_TIME_FORMAT_HR_TIME_HIGHRES = 'hrtimehires';
    const DATE_TIME_FORMAT_FILE = 'file';
    const DATE_TIME_FORMAT_FILE_HIGHRES = 'filehires';
    const DATE_TIME_FORMAT_ISO = 'iso';

    /**
     * @param string|null $format
     *
     * @return string
     */
    public function getCurrentDateTimeFormatted(?string $format = self::DATE_TIME_FORMAT_HR): string
    {
        [, $microseconds] = explode('.', microtime(true));
        $now = new \DateTime();

        switch ($format) {
            case self::DATE_TIME_FORMAT_HR:
                return $now->format('d.m.Y H:i:s');
            case self::DATE_TIME_FORMAT_HR_TIME:
                return $now->format('H:i:s');
            case self::DATE_TIME_FORMAT_HR_TIME_HIGHRES:
                return $now->format('H:i:s.').$microseconds;
            case self::DATE_TIME_FORMAT_FILE:
                return $now->format('Ymd_His');
            case self::DATE_TIME_FORMAT_FILE_HIGHRES:
                return $now->format('Ymd_His_').$microseconds;
            case self::DATE_TIME_FORMAT_ISO:
                return $now->format('"Y-m-d\TH:i:sO"');
        }

        throw new \InvalidArgumentException('UnknownDateFormatRequested');
    }

    /**
     * @param int|null $unixTimestamp
     *
     * @return int
     */
    public function getLoxBerryTimestamp(?int $unixTimestamp = null): int
    {
        if (null === $unixTimestamp) {
            $unixTimestamp = time();
        }

        return $unixTimestamp - self::LOXBERRY_TIME_OFFSET + $this->getTimeZoneOffsetFromUtc();
    }

    /**
     * @param int|null $unixTimestamp
     *
     * @return int
     */
    public function getUnixTimestamp(?int $loxBerryTimestamp = null): int
    {
        if (null === $loxBerryTimestamp) {
            return time();
        }

        return $loxBerryTimestamp + self::LOXBERRY_TIME_OFFSET - $this->getTimeZoneOffsetFromUtc();
    }

    /**
     * @return int
     */
    private function getTimeZoneOffsetFromUtc(): int
    {
        $originDateTimeZone = new \DateTimeZone(date_default_timezone_get());
        $utcDateTimeZone = new \DateTimeZone('UTC');
        $originDateTime = new \DateTime('now', $originDateTimeZone);
        $utcDateTime = new \DateTime('now', $utcDateTimeZone);

        return $originDateTimeZone->getOffset($originDateTime) - $utcDateTimeZone->getOffset($utcDateTime) - 3600;
    }
}
