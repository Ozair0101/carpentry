<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Accounts extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|in:cash,bank')]
    public string $type = 'cash';

    #[Validate('required|numeric')]
    public $opening_balance = 0;

    public bool $is_default = false;

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'type', 'opening_balance', 'is_default']);
        $this->type = 'cash';
        $this->opening_balance = 0;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $a = Account::findOrFail($id);
        $this->editingId = $a->id;
        $this->name = $a->name;
        $this->type = $a->type;
        $this->opening_balance = $a->opening_balance;
        $this->is_default = $a->is_default;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->is_default) {
            Account::where('id', '!=', $this->editingId)->update(['is_default' => false]);
        }

        Account::updateOrCreate(['id' => $this->editingId], $data);

        session()->flash('status', $this->editingId ? 'Account updated.' : 'Account added.');
        $this->showForm = false;
        $this->reset(['editingId', 'name', 'type', 'opening_balance', 'is_default']);
    }

    public function render()
    {
        $accounts = Account::orderByDesc('is_default')->orderBy('name')->get();

        return view('livewire.finance.accounts', [
            'accounts' => $accounts,
            'totalCash' => $accounts->sum(fn (Account $a) => $a->balance()),
        ])->title('Accounts');
    }
}
