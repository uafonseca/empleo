<?php

namespace App\Utility\Graph;

class Month
{
    public static function translateMonth($month)
    {
        $translateMonths = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];

        return $translateMonths[$month];
    }

    public static function getMonths($month, $count = 1)
    {
        $now = new \DateTime();
        $start = \DateTime::createFromFormat('F Y', $month);
        $interval = new \DateInterval(sprintf('P%dM', $count));
        $list = [];

        while ($start <= $now) {
            $m = $start->format('m');

            if (!array_key_exists($m, $list)) {
                $list[$m] = [
                    'month' => $start->format('m'),
                    'name' => self::translateMonth($start->format('F')),
                    'drilldown' => self::translateMonth($start->format('F')),
                    'y' => 0,
                ];
            }

            $start->add($interval);

        }

        return array_values($list);
    }
}