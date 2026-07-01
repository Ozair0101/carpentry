<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Overview extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        // Receivables: what customers still owe us.
        $receivable = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->get()->sum(fn (Invoice $i) => $i->balance());

        // Payables: supplier bills + unpaid salaries.
        $billsPayable = Purchase::whereIn('status', ['unpaid', 'partial'])
            ->get()->sum(fn (Purchase $p) => $p->balance());
        $payrollPayable = (float) \App\Models\Payroll::where('status', 'pending')->sum('net_amount');

        // Employee advances still owed back to the factory.
        $advancesOut = Employee::with('advances')->get()->sum(fn (Employee $e) => $e->advanceBalance());

        // This-month P&L from the ledger.
        $income = (float) Transaction::income()->whereYear('occurred_on', $now->year)->whereMonth('occurred_on', $now->month)->sum('amount');
        $expense = (float) Transaction::expense()->whereYear('occurred_on', $now->year)->whereMonth('occurred_on', $now->month)->sum('amount');

        return view('livewire.finance.overview', [
            'accounts' => $accounts,
            'totalCash' => $accounts->sum(fn (Account $a) => $a->balance()),
            'receivable' => $receivable,
            'billsPayable' => $billsPayable,
            'payrollPayable' => $payrollPayable,
            'advancesOut' => $advancesOut,
            'income' => $income,
            'expense' => $expense,
            'billsDue' => Purchase::with('supplier')->whereIn('status', ['unpaid', 'partial'])
                ->whereNotNull('due_date')->orderBy('due_date')->take(6)->get(),
            'invoicesDue' => Invoice::with('customer')->whereIn('status', ['sent', 'partial', 'overdue'])
                ->orderBy('due_date')->take(6)->get(),
        ])->title('امور مالی');
    }
}
