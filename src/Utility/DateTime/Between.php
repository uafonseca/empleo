<?php

namespace App\Utility\DateTime;

class Between
{
    static function dateIsInBetween(\DateTime $from = null, \DateTime $to = null, \DateTime $subject = null)
    {
        if (!$subject || !$from || !$to) return true;

        return $subject->getTimestamp() >= $from->getTimestamp() && $subject->getTimestamp() <= $to->getTimestamp() ? true : false;
    }
}