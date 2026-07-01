<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Support\Ledger;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    #[Url]
    public string $direction = '';

    #[Url]
    public string $accountFilter = '';

    // Add-transaction modal
    public bool $showForm = false;
    public string $entryType = 'expense'; // income, expense, transfer
    public $account_id = null;
    public $to_account_id = null;
    public $category_id = null;
    public $amount = 0;
    public string $occurred_on = '';
    public string $description = '';

    public function updatingDirection(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function openForm(string $type = 'expense'): void
    {
        $this->reset(['account_id', 'to_account_id', 'category_id', 'amount', 'description']);
        $this->entryType = $type;
        $this->occurred_on = Carbon::today()->format('Y-m-d');
        $this->account_id = Account::default()?->id;
        $this->amount = 0;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'entryType' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required_if:entryType,transfer|nullable|different:account_id|exists:accounts,id',
            'category_id' => 'nullable|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'occurred_on' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        if ($this->entryType === 'transfer') {
            $out = Transaction::create([
                'account_id' => $this->account_id,
                'direction' => 'out',
                'amount' => $this->amount,
                'occurred_on' => $this->occurred_on,
                'description' => $this->description ?: 'Transfer out',
            ]);
            $in = Transaction::create([
                'account_id' => $this->to_account_id,
                'direction' => 'in',
                'amount' => $this->amount,
                'occurred_on' => $this->occurred_on,
                'description' => $this->description ?: 'Transfer in',
                'transfer_id' => $out->id,
            ]);
            $out->update(['transfer_id' => $in->id]);
        } else {
            Ledger::record(
                direction: $this->entryType === 'income' ? 'in' : 'out',
                amount: (float) $this->amount,
                accountId: (int) $this->account_id,
                attributes: [
                    'occurred_on' => $this->occurred_on,
                    'category_id' => $this->category_id ?: null,
                    'description' => $this->description ?: null,
                ],
            );
        }

        $this->showForm = false;
        session()->flash('status', 'Transaction recorded.');
    }

    public function delete(int $id): void
    {
        $tx = Transaction::findOrFail($id);
        // Keep paired transfers consistent.
        if ($tx->transfer_id) {
            Transaction::where('id', $tx->transfer_id)->update(['transfer_id' => null]);
            Transaction::destroy($tx->transfer_id);
        }
        $tx->delete();
        session()->flash('status', 'Transaction deleted.');
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->with(['account', 'category', 'sourceable'])
            ->when($this->direction, fn ($q) => $q->where('direction', $this->direction))
            ->when($this->accountFilter, fn ($q) => $q->where('account_id', $this->accountFilter))
            ->latest('occurred_on')->latest('id')
            ->paginate(20);

        $now = Carbon::now();
        $income = Transaction::income()->whereYear('occurred_on', $now->year)->whereMonth('occurred_on', $now->month)->sum('amount');
        $expense = Transaction::expense()->whereYear('occurred_on', $now->year)->whereMonth('occurred_on', $now->month)->sum('amount');

        return view('livewire.finance.transactions', [
            'transactions' => $transactions,
            'accounts' => Account::orderBy('name')->get(),
            'categories' => TransactionCategory::orderBy('name')->get(),
            'monthIncome' => $income,
            'monthExpense' => $expense,
        ])->title('Transactions');
    }
}
