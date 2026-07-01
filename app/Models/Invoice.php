<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public const STATUSES = ['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('position');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_on');
    }

    public function balance(): float
    {
        return round((float) $this->total - (float) $this->amount_paid, 2);
    }

    /**
     * Recalculate totals from line items, then refresh amount paid and status.
     */
    public function recalculate(): void
    {
        $subtotal = $this->items->sum('line_total');
        $taxable = max(0, $subtotal - (float) $this->discount);
        $taxTotal = round($taxable * ((float) $this->tax_rate / 100), 2);

        $this->subtotal = $subtotal;
        $this->tax_total = $taxTotal;
        $this->total = $taxable + $taxTotal;
        $this->save();

        $this->syncPaymentStatus();
    }

    /**
     * Recompute amount_paid from payments and derive the status. Preserves the
     * draft/cancelled states which are set manually.
     */
    public function syncPaymentStatus(): void
    {
        $paid = (float) $this->payments()->sum('amount');
        $this->amount_paid = $paid;

        if (! in_array($this->status, ['draft', 'cancelled'], true)) {
            if ($paid <= 0) {
                $overdue = $this->due_date && $this->due_date->isPast();
                $this->status = $overdue ? 'overdue' : ($this->status === 'draft' ? 'draft' : 'sent');
            } elseif ($paid + 0.001 >= (float) $this->total) {
                $this->status = 'paid';
            } else {
                $this->status = 'partial';
            }
        }

        $this->save();
    }
}
