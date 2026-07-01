<?php

namespace App\Livewire\Purchases;

use App\Models\Account;
use App\Models\Purchase;
use App\Support\Ledger;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Show extends Component
{
    public Purchase $purchase;

    // Payment modal
    public bool $showPayment = false;
    public $payAmount = 0;
    public string $payDate = '';
    public $payAccountId = null;
    public string $payReference = '';

    public function mount(Purchase $purchase): void
    {
        $this->purchase = $purchase;
        $this->payDate = Carbon::today()->format('Y-m-d');
    }

    public function openPayment(): void
    {
        $this->payAmount = $this->purchase->balance();
        $this->payDate = Carbon::today()->format('Y-m-d');
        $this->payAccountId = Account::default()?->id;
        $this->payReference = '';
        $this->showPayment = true;
    }

    public function recordPayment(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:0.01',
            'payDate' => 'required|date',
            'payAccountId' => 'required|exists:accounts,id',
            'payReference' => 'nullable|string|max:255',
        ]);

        Ledger::record(
            direction: 'out',
            amount: (float) $this->payAmount,
            accountId: (int) $this->payAccountId,
            source: $this->purchase,
            attributes: [
                'occurred_on' => $this->payDate,
                'description' => 'Payment to '.$this->purchase->supplier->name,
                'reference' => $this->payReference ?: null,
            ],
        );

        $this->purchase->syncPaymentStatus();
        $this->showPayment = false;
        session()->flash('status', 'Supplier payment recorded.');
    }

    public function deletePayment(int $transactionId): void
    {
        $this->purchase->payments()->whereKey($transactionId)->delete();
        $this->purchase->syncPaymentStatus();
        session()->flash('status', 'Payment removed.');
    }

    public function delete()
    {
        $this->purchase->delete();
        session()->flash('status', 'Bill deleted.');

        return $this->redirectRoute('bills.index', navigate: true);
    }

    public function render()
    {
        $this->purchase->load(['supplier', 'items', 'payments.account', 'project']);

        return view('livewire.purchases.show', [
            'accounts' => Account::where('is_active', true)->orderBy('name')->get(),
        ])->title($this->purchase->reference ?: 'Bill #'.$this->purchase->id);
    }
}
