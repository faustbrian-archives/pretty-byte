<?php

declare(strict_types=1);

/*
 * This file is part of Pretty Byte.
 *
 * (c) Konceiver Oy <info@konceiver.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Konceiver\PrettyByte;

use Illuminate\Support\Arr;
use Locale;
use NumberFormatter;

final class PrettyByte
{
    const BYTE_UNITS = [
        'B',
        'kB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB',
    ];

    const BIBYTE_UNITS = [
        'B',
        'kiB',
        'MiB',
        'GiB',
        'TiB',
        'PiB',
        'EiB',
        'ZiB',
        'YiB',
    ];

    const BIT_UNITS = [
        'b',
        'kbit',
        'Mbit',
        'Gbit',
        'Tbit',
        'Pbit',
        'Ebit',
        'Zbit',
        'Ybit',
    ];

    const BIBIT_UNITS = [
        'b',
        'kibit',
        'Mibit',
        'Gibit',
        'Tibit',
        'Pibit',
        'Eibit',
        'Zibit',
        'Yibit',
    ];

    private static function toLocaleString($number, $locale)
    {
        $result = $number;

        if (is_string($locale)) {
            $result = (new NumberFormatter($locale, NumberFormatter::DECIMAL))->format($number);
        } elseif ($locale === true) {
            $result = (new NumberFormatter(Locale::getDefault(), NumberFormatter::DECIMAL))->format($number);
        }

        return $result;
    }

    private static function toPrecision($number, $precision)
    {
        if ($number === 0) {
            return 0;
        }

        $exponent    = floor(log10(abs($number)) + 1);
        $significand = round(($number / pow(10, $exponent)) * pow(10, $precision)) / pow(10, $precision);

        return $significand * pow(10, $exponent);
    }

    public static function format($number, array $options = []): string
    {
        $options = array_merge(['bits' => false, 'binary' => false], $options);

        $UNITS = $options['bits'] ?
            ($options['binary'] ? static::BIBIT_UNITS : static::BIT_UNITS) :
            ($options['binary'] ? static::BIBYTE_UNITS : static::BYTE_UNITS);

        if (Arr::get($options, 'signed', false) && $number === 0) {
            return ' 0 '.$UNITS[0];
        }

        $isNegative = $number < 0;
        $prefix     = $isNegative ? '-' : (Arr::get($options, 'signed', false) ? '+' : '');

        if ($isNegative) {
            $number = -$number;
        }

        if ($number < 1) {
            $numberString = static::toLocaleString($number, Arr::get($options, 'locale'));

            return $prefix.$numberString.' '.$UNITS[0];
        }

        $exponent     = min(floor($options['binary'] ? log($number) / log(1024) : log10($number) / 3), count($UNITS) - 1);
        $number       = static::toPrecision(($number / pow($options['binary'] ? 1024 : 1000, $exponent)), 3);
        $numberString = static::toLocaleString($number, Arr::get($options, 'locale'));

        $unit = $UNITS[$exponent];

        return $prefix.$numberString.' '.$unit;
    }
}
