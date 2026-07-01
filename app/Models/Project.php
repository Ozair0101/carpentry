<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A carpentry "Job" (labelled "Job" in the UI). Stored as `projects` to avoid
 * colliding with Laravel's framework `jobs` queue table.
 */
class Project extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public const STATUSES = ['lead', 'scheduled', 'in_progress', 'on_hold', 'completed', 'cancelled'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class)->orderBy('position');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class)->latest('incurred_on');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('starts_at');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    /** Total actual cost logged against this job. */
    public function actualCost(): float
    {
        return (float) $this->expenses()->sum('total');
    }
}
