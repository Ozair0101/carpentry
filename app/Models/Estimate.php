<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estimate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = ['draft', 'sent', 'approved', 'rejected'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class)->orderBy('position');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    /**
     * Recalculate subtotal, tax and total from the line items.
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
    }
}
