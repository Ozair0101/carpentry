<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DocumentNumber
{
    /**
     * Generate and reserve the next estimate number, e.g. "EST-0001".
     */
    public static function nextEstimate(): string
    {
        return static::next('estimate');
    }

    /**
     * Generate and reserve the next invoice number, e.g. "INV-0001".
     */
    public static function nextInvoice(): string
    {
        return static::next('invoice');
    }

    protected static function next(string $type): string
    {
        return DB::transaction(function () use ($type) {
            /** @var Setting $settings */
            $settings = Setting::query()->lockForUpdate()->firstOrCreate(['id' => 1]);

            $column = "next_{$type}_number";
            $prefixColumn = "{$type}_prefix";

            $number = (int) $settings->{$column};
            $settings->{$column} = $number + 1;
            $settings->save();

            return $settings->{$prefixColumn}.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        });
    }
}
