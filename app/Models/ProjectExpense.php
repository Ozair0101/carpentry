<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExpense extends Model
{
    protected $guarded = [];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'incurred_on' => 'date',
    ];

    public const TYPES = ['material', 'labour', 'subcontractor', 'other'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
