<?php
namespace App\Helpers;

class NumberHelper
{
    public static function spellOutArabic($number)
    {
        $formatter = new \NumberFormatter('ar_EG', \NumberFormatter::SPELLOUT);
        return $formatter->format($number);
    }
}