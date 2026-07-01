<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Payroll extends Model
{
    protected $guarded = [];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_on' => 'date',
        'base_amount' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** The ledger payment(s) for this salary run. */
    public function payments(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'sourceable')->where('direction', 'out');
    }

    public function computeNet(): float
    {
        return round(
            (float) $this->base_amount + (float) $this->bonus
            - (float) $this->deductions - (float) $this->advance_deducted,
            2
        );
    }
}
