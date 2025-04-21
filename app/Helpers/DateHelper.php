<?php

use Carbon\Carbon;

function formatKhmerDate($date)
{
    // Set the Carbon instance from the input date
    $carbonDate = Carbon::parse($date);

    // Define Khmer month names
    $khmerMonths = [
        1 => 'មករា',
        2 => 'កុម្ភៈ',
        3 => 'មីនា',
        4 => 'មេសា',
        5 => 'ឧសភា',
        6 => 'มิថុនា',
        7 => 'កក្កដា',
        8 => 'សីហា',
        9 => 'កញ្ញា',
        10 => 'តុលា',
        11 => 'វិច្ឆិកា',
        12 => 'ធ្នូ'
    ];

    // Get the day, month, and year in Khmer
    $day = $carbonDate->day;
    $month = $khmerMonths[$carbonDate->month];
    $year = $carbonDate->year;

    // Convert day to Khmer digits
    $khmerDay = convertToKhmerDigits($day);
    $khmerYear = convertToKhmerDigits($year);

    return "ថ្ងៃទី{$khmerDay} {$month} ឆ្នាំ{$khmerYear}";
}

/**
 * Convert a number to Khmer digits.
 *
 * @param int $number
 * @return string
 */
function convertToKhmerDigits($number)
{
    $khmerDigits = [
        '0' => '០',
        '1' => '១',
        '2' => '២',
        '3' => '៣',
        '4' => '៤',
        '5' => '៥',
        '6' => '៦',
        '7' => '៧',
        '8' => '៨',
        '9' => '៩'
    ];

    $numberStr = (string) $number;
    $khmerNumberStr = '';

    foreach (str_split($numberStr) as $digit) {
        $khmerNumberStr .= $khmerDigits[$digit];
    }

    return $khmerNumberStr;
}
