<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $guarded = [];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class)->latest('bill_date');
    }

    /** Total still owed to this supplier across unpaid/partial bills. */
    public function balanceOwed(): float
    {
        return round($this->purchases()
            ->whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn (Purchase $p) => $p->balance()), 2);
    }
}
