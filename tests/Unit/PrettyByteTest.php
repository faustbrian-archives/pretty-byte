<?php

declare(strict_types=1);

use Konceiver\PrettyByte\PrettyByte;

test('converts bytes to human readable strings', function ($input, $output): void {
    expect(PrettyByte::format($input))->toBe($output);
})->with([
    [0, '0 B'],
    [0.4, '0.4 B'],
    [0.7, '0.7 B'],
    [10, '10 B'],
    [10.1, '10.1 B'],
    [999, '999 B'],
    [1001, '1 kB'],
    [1e16, '10 PB'],
    [1e30, '1000000 YB'],
]);

test('supports negative number', function ($input, $output): void {
    expect(PrettyByte::format($input))->toBe($output);
})->with([
    [-0.4, '-0.4 B'],
    [-0.7, '-0.7 B'],
    [-10.1, '-10.1 B'],
    [-999, '-999 B'],
    [-1001, '-1 kB'],
]);

test('locale option', function ($input, $locale, $output): void {
    Locale::setDefault('en_US');

    expect(PrettyByte::format($input, ['locale' => $locale]))->toBe($output);
})->with([
    [-0.4, 'de-DE', '-0,4 B'],
    [0.4, 'de-DE', '0,4 B'],
    [1001, 'de-DE', '1 kB'],
    [10.1, 'de-DE', '10,1 B'],
    [1e30, 'de-DE', '1.000.000 YB'],

    [-0.4, 'en-US', '-0.4 B'],
    [0.4, 'en-US', '0.4 B'],
    [1001, 'en-US', '1 kB'],
    [10.1, 'en-US', '10.1 B'],
    [1e30, 'en-US', '1,000,000 YB'],

    [-0.4, true, '-0.4 B'],
    [0.4, true, '0.4 B'],
    [1001, true, '1 kB'],
    [10.1, true, '10.1 B'],
    [1e30, true, '1,000,000 YB'],

    [-0.4, false, '-0.4 B'],
    [0.4, false, '0.4 B'],
    [1001, false, '1 kB'],
    [10.1, false, '10.1 B'],
    [1e30, false, '1000000 YB'],

    [-0.4, null, '-0.4 B'],
    [0.4, null, '0.4 B'],
    [1001, null, '1 kB'],
    [10.1, null, '10.1 B'],
    [1e30, null, '1000000 YB'],
]);

test('signed option', function ($input, $output): void {
    expect(PrettyByte::format($input, ['signed' => true]))->toBe($output);
})->with([
    [42,  '+42 B'],
    [-13, '-13 B'],
    [0,  ' 0 B'],
]);

test('bits option', function ($input, $output): void {
    expect(PrettyByte::format($input, ['bits' => true]))->toBe($output);
})->with([
    [0, '0 b'],
    [0.4, '0.4 b'],
    [0.7, '0.7 b'],
    [10, '10 b'],
    [10.1, '10.1 b'],
    [999, '999 b'],
    [1001, '1 kbit'],
    [1001, '1 kbit'],
    [1e16, '10 Pbit'],
    [1e30, '1000000 Ybit'],
]);

test('binary option', function ($input, $output): void {
    expect(PrettyByte::format($input, ['binary' => true]))->toBe($output);
})->with([
    [0, '0 B'],
    [4, '4 B'],
    [10, '10 B'],
    [10.1, '10.1 B'],
    [999, '999 B'],
    [1025, '1 kiB'],
    [1001, '1000 B'],
    [1e16, '8.88 PiB'],
    [1e30, '827000 YiB'],
]);

test('bits and binary option', function ($input, $output): void {
    expect(PrettyByte::format($input, ['bits' => true, 'binary' => true]))->toBe($output);
})->with([
    [0, '0 b'],
    [4, '4 b'],
    [10, '10 b'],
    [999, '999 b'],
    [1025, '1 kibit'],
    [1e6, '977 kibit'],
]);
