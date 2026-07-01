<?php

namespace App\Livewire\Invoices;

use App\Livewire\Concerns\ManagesLineItems;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Material;
use App\Models\Setting;
use App\Support\DocumentNumber;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Form extends Component
{
    use ManagesLineItems;

    public ?Invoice $invoice = null;

    public $customer_id = null;

    public string $status = 'draft';

    public string $issue_date = '';

    public ?string $due_date = null;

    public string $notes = '';

    public string $terms = '';

    public function mount(?Invoice $invoice = null): void
    {
        $settings = Setting::current();

        if ($invoice && $invoice->exists) {
            $this->invoice = $invoice;
            $this->customer_id = $invoice->customer_id;
            $this->status = $invoice->status;
            $this->issue_date = $invoice->issue_date->format('Y-m-d');
            $this->due_date = $invoice->due_date?->format('Y-m-d');
            $this->notes = (string) $invoice->notes;
            $this->terms = (string) $invoice->terms;
            $this->discount = $invoice->discount;
            $this->tax_rate = $invoice->tax_rate;
            $this->items = $invoice->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'description' => $i->description,
                'qty' => $i->qty,
                'unit' => $i->unit,
                'unit_price' => $i->unit_price,
            ])->toArray();
        } else {
            $this->customer_id = request()->integer('customer') ?: null;
            $this->issue_date = Carbon::today()->format('Y-m-d');
            $this->due_date = Carbon::today()->addDays(14)->format('Y-m-d');
            $this->tax_rate = $settings->tax_rate;
            $this->terms = (string) $settings->default_terms;
            $this->addItem();
        }
    }

    public function save()
    {
        $this->validate(array_merge([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:'.implode(',', Invoice::STATUSES),
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
        ], $this->itemRules()));

        $data = [
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date ?: null,
            'notes' => $this->notes ?: null,
            'terms' => $this->terms ?: null,
            'discount' => (float) $this->discount,
            'tax_rate' => (float) $this->tax_rate,
        ];

        if ($this->invoice) {
            $this->invoice->update($data);
            $invoice = $this->invoice;
        } else {
            $invoice = Invoice::create($data + ['number' => DocumentNumber::nextInvoice()]);
        }

        $this->saveItemsTo($invoice);
        $invoice->load('items');
        $invoice->recalculate();

        session()->flash('status', $this->invoice ? 'فاکتور به‌روزرسانی شد.' : 'فاکتور ایجاد شد.');

        return $this->redirectRoute('invoices.show', $invoice, navigate: true);
    }

    public function render()
    {
        return view('livewire.invoices.form', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'materials' => Material::orderBy('name')->get(['id', 'name', 'unit', 'unit_price']),
        ])->title($this->invoice ? 'ویرایش فاکتور' : 'فاکتور جدید');
    }
}
