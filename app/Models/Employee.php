<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'salary_rate' => 'decimal:2',
        'joined_on' => 'date',
        'is_active' => 'boolean',
    ];

    public const SALARY_TYPES = ['monthly', 'daily', 'hourly'];

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class)->latest('period_end');
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class)->latest('advanced_on');
    }

    /** Outstanding advances the employee still owes back. */
    public function advanceBalance(): float
    {
        return round($this->advances()->get()->sum(fn (EmployeeAdvance $a) => $a->outstanding()), 2);
    }

    /** Salary runs recorded but not yet paid. */
    public function unpaidPayroll(): float
    {
        return (float) $this->payrolls()->where('status', 'pending')->sum('net_amount');
    }

    /**
     * Recover a deducted advance against outstanding balances, oldest first.
     * Called after a payroll that deducted an advance is actually paid.
     */
    public function recoverAdvances(float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        foreach ($this->advances()->reorder('advanced_on')->get() as $advance) {
            if ($amount <= 0) {
                break;
            }
            $take = min($advance->outstanding(), $amount);
            if ($take > 0) {
                $advance->increment('recovered', $take);
                $amount -= $take;
            }
        }
    }
}
