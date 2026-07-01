<?php

namespace App\Models;

use App\Support\Ledger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_on' => 'date',
    ];

    public const METHODS = ['cash', 'bank', 'card', 'cheque', 'other'];

    protected static function booted(): void
    {
        // Post customer payments into the money ledger (income), and remove the
        // ledger entry if the payment is deleted.
        static::created(function (Payment $payment) {
            $transaction = Ledger::record(
                direction: 'in',
                amount: (float) $payment->amount,
                accountId: $payment->account_id,
                source: $payment,
                attributes: [
                    'occurred_on' => $payment->paid_on,
                    'description' => 'Payment for invoice '.$payment->invoice?->number,
                    'reference' => $payment->reference,
                ],
            );

            // Backfill account_id with the resolved default without re-triggering events.
            if ($transaction && ! $payment->account_id) {
                $payment->newQuery()->whereKey($payment->getKey())->update(['account_id' => $transaction->account_id]);
            }
        });

        static::deleting(function (Payment $payment) {
            $payment->transaction()->delete();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'sourceable');
    }
}
