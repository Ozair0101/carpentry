<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $guarded = [];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPES = ['cash', 'bank'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** Current balance = opening + money in − money out. */
    public function balance(): float
    {
        $in = (float) $this->transactions()->where('direction', 'in')->sum('amount');
        $out = (float) $this->transactions()->where('direction', 'out')->sum('amount');

        return round((float) $this->opening_balance + $in - $out, 2);
    }

    /** The account to post to when none is chosen explicitly. */
    public static function default(): ?self
    {
        return static::where('is_active', true)->orderByDesc('is_default')->orderBy('id')->first();
    }
}
