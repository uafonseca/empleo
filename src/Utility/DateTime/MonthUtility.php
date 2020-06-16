<?php

namespace App\Utility\DateTime;

class MonthUtility
{
    public static function getMonths()
    {
        return [
            ['value' => 0, 'text' => 'Enero'],
            ['value' => 1, 'text' => 'Febrero'],
            ['value' => 2, 'text' => 'Marzo'],
            ['value' => 3, 'text' => 'Abril'],
            ['value' => 4, 'text' => 'Mayo'],
            ['value' => 5, 'text' => 'Junio'],
            ['value' => 6, 'text' => 'Julio'],
            ['value' => 7, 'text' => 'Agosto'],
            ['value' => 8, 'text' => 'Septiembre'],
            ['value' => 9, 'text' => 'Octubre'],
            ['value' => 10, 'text' => 'Noviembre'],
            ['value' => 11, 'text' => 'Diciembre'],
        ];
    }

    // TODO Change this code later. This is the offline solution :)
    public static function getMonthsForFormWidget()
    {
        return [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
    }

    /**
     * @param $index
     * @return mixed
     */
    public static function getMonthName($index)
    {
        foreach (self::getMonths() as $month) {
            if ($month['value'] == $index) {
                return $month['text'];
            }
        }
    }


    /**
     * @return array
     */
    public static function getMonthsWithYear()
    {
        $year = date('Y');
        return [
            'Enero '.$year => 'Enero '.$year,
            'Febrero '.$year => 'Febrero '.$year,
            'Marzo '.$year => 'Marzo '.$year,
            'Abril '.$year => 'Abril '.$year,
            'Mayo '.$year => 'Mayo '.$year,
            'Junio '.$year => 'Junio '.$year,
            'Julio '.$year => 'Julio '.$year,
            'Agosto '.$year => 'Agosto '.$year,
            'Septiembre '.$year => 'Septiembre '.$year,
            'Octubre'.$year => 'Octubre '.$year,
            'Noviembre'.$year => 'Noviembre '.$year,
            'Diciembre '.$year => 'Diciembre '.$year
        ];
    }
}