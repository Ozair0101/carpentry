<?php

namespace App\Support;

use App\Models\Account;
use App\Models\EmployeeAdvance;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

/**
 * Financial reports derived from the single money ledger (transactions) plus the
 * accrual documents (invoices, bills, payroll).
 *
 * The income statement is CASH-BASIS: it reflects money that actually moved.
 * Transfers between the workshop's own accounts and staff advances (which are
 * loans, not expenses) are excluded so profit is not distorted.
 */
class Reports
{
    /* ---------------------------------------------------------------------
     | Income statement (profit & loss) — cash basis over a date range
     * ------------------------------------------------------------------- */
    public static function incomeStatement(Carbon $from, Carbon $to): array
    {
        $rows = Transaction::query()
            ->with('category')
            ->whereNull('transfer_id')
            ->whereBetween('occurred_on', [$from->toDateString(), $to->toDateString()])
            ->get();

        $revenue = [];
        $expense = [];

        foreach ($rows as $t) {
            if ($t->direction === 'in') {
                $label = self::incomeLabel($t);
                $revenue[$label] = round(($revenue[$label] ?? 0) + (float) $t->amount, 2);
            } else {
                // Advances are loans to staff, not an operating expense.
                if ($t->sourceable_type === EmployeeAdvance::class) {
                    continue;
                }
                $label = self::expenseLabel($t);
                $expense[$label] = round(($expense[$label] ?? 0) + (float) $t->amount, 2);
            }
        }

        arsort($revenue);
        arsort($expense);

        $revenueTotal = round(array_sum($revenue), 2);
        $expenseTotal = round(array_sum($expense), 2);

        return [
            'from' => $from,
            'to' => $to,
            'revenue' => $revenue,
            'expense' => $expense,
            'revenueTotal' => $revenueTotal,
            'expenseTotal' => $expenseTotal,
            'net' => round($revenueTotal - $expenseTotal, 2),
        ];
    }

    /* ---------------------------------------------------------------------
     | Balance sheet — a point-in-time snapshot of what the workshop owns
     | (assets) and owes (liabilities). Net worth (equity) is the balance.
     * ------------------------------------------------------------------- */
    public static function balanceSheet(Carbon $asOf): array
    {
        $date = $asOf->toDateString();

        // Cash & bank: opening balance + money in − money out up to the date.
        $accounts = Account::where('is_active', true)->orderBy('name')->get()
            ->map(function (Account $a) use ($date) {
                $in = (float) $a->transactions()->where('direction', 'in')->where('occurred_on', '<=', $date)->sum('amount');
                $out = (float) $a->transactions()->where('direction', 'out')->where('occurred_on', '<=', $date)->sum('amount');

                return ['name' => $a->name, 'balance' => round((float) $a->opening_balance + $in - $out, 2)];
            });
        $cashTotal = round($accounts->sum('balance'), 2);

        // Accounts receivable: unpaid customer invoice balances as of the date.
        $receivable = Invoice::where('issue_date', '<=', $date)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->with('payments')->get()
            ->sum(function (Invoice $inv) use ($asOf) {
                $paid = $inv->payments->filter(fn ($p) => $p->paid_on && $p->paid_on->lte($asOf))->sum('amount');

                return max(0, round((float) $inv->total - (float) $paid, 2));
            });

        // Employee advances still owed back to the workshop.
        $advancesReceivable = EmployeeAdvance::where('advanced_on', '<=', $date)->get()
            ->sum(fn (EmployeeAdvance $a) => max(0, $a->outstanding()));

        // Accounts payable: unpaid supplier bill balances as of the date.
        $payable = Purchase::where('bill_date', '<=', $date)
            ->where('status', '!=', 'cancelled')
            ->with('payments')->get()
            ->sum(function (Purchase $p) use ($asOf) {
                $paid = $p->payments->filter(fn ($t) => $t->occurred_on && $t->occurred_on->lte($asOf))->sum('amount');

                return max(0, round((float) $p->total - (float) $paid, 2));
            });

        // Salaries recorded but not fully paid — the outstanding balance as of date.
        $salariesPayable = Payroll::with('payments')
            ->whereIn('status', ['pending', 'partial'])
            ->where(fn ($q) => $q->whereNull('period_end')->orWhere('period_end', '<=', $date))
            ->get()
            ->sum(function (Payroll $p) use ($asOf) {
                $paid = $p->payments->filter(fn ($t) => $t->occurred_on && $t->occurred_on->lte($asOf))->sum('amount');

                return max(0, round((float) $p->net_amount - (float) $paid, 2));
            });

        $assets = [
            'cashAccounts' => $accounts,
            'cashTotal' => $cashTotal,
            'receivable' => round((float) $receivable, 2),
            'advancesReceivable' => round((float) $advancesReceivable, 2),
        ];
        $assetsTotal = round($cashTotal + $assets['receivable'] + $assets['advancesReceivable'], 2);

        $liabilities = [
            'payable' => round((float) $payable, 2),
            'salariesPayable' => round($salariesPayable, 2),
        ];
        $liabilitiesTotal = round($liabilities['payable'] + $liabilities['salariesPayable'], 2);

        return [
            'asOf' => $asOf,
            'assets' => $assets,
            'assetsTotal' => $assetsTotal,
            'liabilities' => $liabilities,
            'liabilitiesTotal' => $liabilitiesTotal,
            'equity' => round($assetsTotal - $liabilitiesTotal, 2),
        ];
    }

    /* ---------------------------------------------------------------------
     | Monthly report — income, expense and profit for each month of a year.
     * ------------------------------------------------------------------- */
    public static function monthly(int $year): array
    {
        $months = [];
        $totalIn = 0.0;
        $totalOut = 0.0;

        for ($m = 1; $m <= 12; $m++) {
            $in = (float) self::incomeBase()
                ->whereYear('occurred_on', $year)->whereMonth('occurred_on', $m)->sum('amount');
            $out = (float) self::expenseBase()
                ->whereYear('occurred_on', $year)->whereMonth('occurred_on', $m)->sum('amount');

            $months[] = [
                'month' => $m,
                'label' => Carbon::create($year, $m, 1)->translatedFormat('F'),
                'income' => round($in, 2),
                'expense' => round($out, 2),
                'net' => round($in - $out, 2),
            ];
            $totalIn += $in;
            $totalOut += $out;
        }

        return [
            'year' => $year,
            'months' => $months,
            'incomeTotal' => round($totalIn, 2),
            'expenseTotal' => round($totalOut, 2),
            'netTotal' => round($totalIn - $totalOut, 2),
            // Largest single-month income, used to scale the mini bar chart.
            'peak' => round(max(1, collect($months)->max('income'), collect($months)->max('expense')), 2),
        ];
    }

    /* ------------------------------ helpers ----------------------------- */

    /** Ledger income, excluding inter-account transfers. */
    protected static function incomeBase()
    {
        return Transaction::query()->where('direction', 'in')->whereNull('transfer_id');
    }

    /** Ledger spending, excluding transfers and staff advances (loans). */
    protected static function expenseBase()
    {
        return Transaction::query()->where('direction', 'out')->whereNull('transfer_id')
            ->where(fn ($q) => $q->whereNull('sourceable_type')->orWhere('sourceable_type', '!=', EmployeeAdvance::class));
    }

    protected static function incomeLabel(Transaction $t): string
    {
        if ($t->sourceable_type === Payment::class) {
            return 'عواید فروش و خدمات';
        }

        return $t->category?->name ?: 'سایر عواید';
    }

    protected static function expenseLabel(Transaction $t): string
    {
        return match ($t->sourceable_type) {
            Purchase::class => 'خرید مواد و اجناس',
            Payroll::class => 'معاشات کارکنان',
            default => $t->category?->name ?: 'سایر مصارف',
        };
    }
}
