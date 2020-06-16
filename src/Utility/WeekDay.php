<?php

namespace App\Utility;

class WeekDay
{
    public static $days = [
        'Lunes' => 0,
        'Martes' => 1,
        'Miercoles' => 2,
        'Jueves' => 3,
        'Viernes' => 4,
        'Sábado' => 5,
        'Domingo' => 6,
    ];

    public static $dayFromNumber = [
        0 => 'Lunes',
        1 => 'Martes',
        2 => 'Miercoles',
        3 => 'Jueves',
        4 => 'Viernes',
        5 => 'Sábado',
        6 => 'Domingo',
    ];
}
