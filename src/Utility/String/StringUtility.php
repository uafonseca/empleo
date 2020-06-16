<?php

namespace App\Utility\String;

class StringUtility
{
    public static function strToLowDash($string)
    {
        return str_replace(' ', '_', strtolower($string));
    }
}