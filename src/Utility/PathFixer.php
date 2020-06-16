<?php

namespace App\Utility;

class PathFixer
{
    public static function fix($path)
    {
        return ('/' !== substr($path, -1)) ? $path . '/' : $path;
    }
}
