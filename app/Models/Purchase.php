<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = ['unpaid', 'partial', 'paid', 'cancelled'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class)->orderBy('position');
    }

    /** Payments made against this bill live in the money ledger. */
    public function payments(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'sourceable')->where('direction', 'out');
    }

    public function amountPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return round((float) $this->total - $this->amountPaid(), 2);
    }

    /** Recalculate totals from line items. */
    public function recalculate(): void
    {
        $this->subtotal = $this->items->sum('line_total');
        $this->total = round((float) $this->subtotal + (float) $this->tax_total, 2);
        $this->save();
        $this->syncPaymentStatus();
    }

    public function syncPaymentStatus(): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        $paid = $this->amountPaid();

        if ($paid <= 0) {
            $this->status = 'unpaid';
        } elseif ($paid + 0.001 >= (float) $this->total) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
