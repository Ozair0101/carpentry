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
        'overtime' => 'decimal:2',
        'deductions' => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public const STATUSES = ['pending', 'partial', 'paid'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** The ledger payment(s) for this salary run. */
    public function payments(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'sourceable')->where('direction', 'out');
    }

    /** Net owed for the period: base + bonus + overtime − deductions − advance. */
    public function computeNet(): float
    {
        return round(
            (float) $this->base_amount + (float) $this->bonus + (float) $this->overtime
            - (float) $this->deductions - (float) $this->advance_deducted,
            2
        );
    }

    /** Total actually paid out to the employee so far. */
    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Outstanding balance. Positive = we still owe the employee (pay later);
     * negative = we overpaid them.
     */
    public function balance(): float
    {
        return round((float) $this->net_amount - $this->amountPaid(), 2);
    }

    /**
     * Recompute status from how much has been paid:
     * pending (nothing paid) → partial (paid, but we still owe) → paid.
     */
    public function syncPaymentStatus(): void
    {
        $paid = $this->amountPaid();

        if ($paid <= 0) {
            $this->status = 'pending';
        } elseif ($paid + 0.001 >= (float) $this->net_amount) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
