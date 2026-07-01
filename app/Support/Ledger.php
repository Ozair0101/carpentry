<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ledger
{
    /**
     * Record a money movement in the ledger.
     *
     * Resolves a default account when none is given. Returns null (and records
     * nothing) if there is no account at all yet — so payments never fail just
     * because finance accounts haven't been set up.
     */
    public static function record(
        string $direction,
        float $amount,
        ?int $accountId,
        ?Model $source = null,
        array $attributes = []
    ): ?Transaction {
        $account = $accountId ? Account::find($accountId) : Account::default();

        if (! $account) {
            return null;
        }

        return Transaction::create(array_merge([
            'account_id' => $account->id,
            'direction' => $direction,
            'amount' => round($amount, 2),
            'occurred_on' => $attributes['occurred_on'] ?? Carbon::today(),
            'sourceable_type' => $source?->getMorphClass(),
            'sourceable_id' => $source?->getKey(),
        ], $attributes));
    }
}
