<?php

namespace App\Livewire\Invoices;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Show extends Component
{
    public Invoice $invoice;

    // Payment form
    public bool $showPayment = false;

    public $payAmount = 0;

    public string $payDate = '';

    public string $payMethod = 'cash';

    public $payAccountId = null;

    public string $payReference = '';

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->payDate = Carbon::today()->format('Y-m-d');
        $this->payAmount = $invoice->balance();
    }

    public function openPayment(): void
    {
        $this->payAmount = $this->invoice->balance();
        $this->payDate = Carbon::today()->format('Y-m-d');
        $this->payMethod = 'cash';
        $this->payAccountId = Account::default()?->id;
        $this->payReference = '';
        $this->showPayment = true;
    }

    public function recordPayment(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:0.01',
            'payDate' => 'required|date',
            'payMethod' => 'required|in:'.implode(',', Payment::METHODS),
            'payAccountId' => 'nullable|exists:accounts,id',
            'payReference' => 'nullable|string|max:255',
        ]);

        // Move a draft invoice to "sent" implicitly once payment is taken.
        if ($this->invoice->status === 'draft') {
            $this->invoice->status = 'sent';
        }

        $this->invoice->payments()->create([
            'amount' => (float) $this->payAmount,
            'paid_on' => $this->payDate,
            'method' => $this->payMethod,
            'account_id' => $this->payAccountId ?: null,
            'reference' => $this->payReference ?: null,
        ]);

        $this->invoice->syncPaymentStatus();
        $this->showPayment = false;

        session()->flash('status', 'پرداخت ثبت شد.');
    }

    public function deletePayment(int $id): void
    {
        $this->invoice->payments()->findOrFail($id)->delete();
        $this->invoice->syncPaymentStatus();
        session()->flash('status', 'پرداخت حذف شد.');
    }

    public function setStatus(string $status): void
    {
        abort_unless(in_array($status, Invoice::STATUSES, true), 400);
        $this->invoice->update(['status' => $status]);
        $this->invoice->syncPaymentStatus();
    }

    public function delete()
    {
        $this->invoice->delete();
        session()->flash('status', 'فاکتور حذف شد.');

        return $this->redirectRoute('invoices.index', navigate: true);
    }

    public function render()
    {
        $this->invoice->load(['customer', 'items', 'payments', 'project']);

        return view('livewire.invoices.show', [
            'accounts' => Account::where('is_active', true)->orderBy('name')->get(),
        ])->title($this->invoice->number);
    }
}
