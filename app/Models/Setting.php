<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'next_estimate_number' => 'integer',
        'next_invoice_number' => 'integer',
    ];

    /**
     * Get the single settings row, creating it with defaults if missing.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
