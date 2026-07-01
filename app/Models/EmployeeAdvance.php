<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'recovered' => 'decimal:2',
        'advanced_on' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** Amount still owed back by the employee. */
    public function outstanding(): float
    {
        return round((float) $this->amount - (float) $this->recovered, 2);
    }
}
