<?php

namespace App\Utility;

use DateTime;

class TimeUtility
{
    public static function toMinutes(DateTime $time)
    {
        $time = explode(':', $time->format('H:i:s'));
        $hours = (int)($time[0]);
        $mins = (int)($time[1]);
        $seconds = (int)($time[2]);

        return ($hours * 60) + $mins + ($seconds / 60);
    }

    public static function arrayToDateTime(array $date)
    {
        return new DateTime(date($date['year'] . '-' . $date['month'] . '-' . $date['day']));
    }
}
