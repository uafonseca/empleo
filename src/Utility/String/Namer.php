<?php

namespace App\Utility\String;

class Namer
{
    public static function name($length = 10)
    {
        $name = hash('sha1', self::getRandomString());
        if (null !== $length) {
            $name = substr($name, 0, $length);
        }

        return $name;
    }

    public static function getRandomString()
    {
        return microtime(true) . mt_rand(0, 9999999);
    }
}
