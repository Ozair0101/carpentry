<?php

namespace App\Support;

use App\Models\Setting;

class Format
{
    protected static ?string $currency = null;

    /**
     * Format a numeric value as money using the configured currency symbol.
     */
    public static function money(int|float|string|null $value): string
    {
        $symbol = static::symbol();

        return $symbol.number_format((float) $value, 2);
    }

    /**
     * Resolve the currency symbol from settings (cached for the request).
     */
    public static function symbol(): string
    {
        if (static::$currency !== null) {
            return static::$currency;
        }

        $code = optional(Setting::query()->find(1))->currency ?? 'USD';

        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'AUD' => 'A$',
            'CAD' => 'C$', 'INR' => '₹', 'PKR' => '₨', 'AED' => 'د.إ',
            'NZD' => 'NZ$', 'ZAR' => 'R',
        ];

        return static::$currency = $symbols[$code] ?? ($code.' ');
    }
}
