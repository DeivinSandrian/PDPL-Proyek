<?php

namespace App\Support;

class MoneyFormatter
{
    /**
     * Format an amount as Indonesian Rupiah (IDR).
     *
     * Uses Indonesian locale conventions: dot as thousands separator and
     * comma as the decimal separator, with two decimal places.
     *
     * @param  float|string  $amount
     * @return string e.g. "Rp 1.234.567,89"
     */
    public static function idr(float|string $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 2, ',', '.');
    }
}
