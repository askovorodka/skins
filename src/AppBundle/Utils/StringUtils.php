<?php
namespace AppBundle\Utils;

final class StringUtils
{
    public static function round($number): float
    {
        $number = str_replace(" ", "", $number);
        $number = str_replace(",", "", $number);
        $number = intval($number * 100) / 100;
        return (float)$number;
    }

    public static function prepareNumber(string $number): float
    {
        $number = str_replace(" ", "", $number);
        $number = str_replace(",","", $number);
        return (float) $number;
    }
}