<?php

namespace App\Livewire\Estimates;

use App\Livewire\Concerns\ManagesLineItems;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Material;
use App\Models\Setting;
use App\Support\DocumentNumber;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    use ManagesLineItems;

    public ?Estimate $estimate = null;

    #[Validate('required|exists:customers,id')]
    public $customer_id = null;

    #[Validate('required|in:draft,sent,approved,rejected')]
    public string $status = 'draft';

    #[Validate('required|date')]
    public string $issue_date = '';

    #[Validate('nullable|date')]
    public ?string $valid_until = null;

    #[Validate('nullable|string')]
    public string $notes = '';

    #[Validate('nullable|string')]
    public string $terms = '';

    public function mount(?Estimate $estimate = null): void
    {
        $settings = Setting::current();

        if ($estimate && $estimate->exists) {
            $this->estimate = $estimate;
            $this->customer_id = $estimate->customer_id;
            $this->status = $estimate->status;
            $this->issue_date = $estimate->issue_date->format('Y-m-d');
            $this->valid_until = $estimate->valid_until?->format('Y-m-d');
            $this->notes = (string) $estimate->notes;
            $this->terms = (string) $estimate->terms;
            $this->discount = $estimate->discount;
            $this->tax_rate = $estimate->tax_rate;
            $this->items = $estimate->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'description' => $i->description,
                'qty' => $i->qty,
                'unit' => $i->unit,
                'unit_price' => $i->unit_price,
            ])->toArray();
        } else {
            $this->customer_id = request()->integer('customer') ?: null;
            $this->issue_date = Carbon::today()->format('Y-m-d');
            $this->valid_until = Carbon::today()->addDays(30)->format('Y-m-d');
            $this->tax_rate = $settings->tax_rate;
            $this->terms = (string) $settings->default_terms;
            $this->addItem();
        }
    }

    public function save()
    {
        $this->validate(array_merge([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:draft,sent,approved,rejected',
            'issue_date' => 'required|date',
            'valid_until' => 'nullable|date',
        ], $this->itemRules()));

        $data = [
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'issue_date' => $this->issue_date,
            'valid_until' => $this->valid_until ?: null,
            'notes' => $this->notes ?: null,
            'terms' => $this->terms ?: null,
            'discount' => (float) $this->discount,
            'tax_rate' => (float) $this->tax_rate,
        ];

        if ($this->estimate) {
            $this->estimate->update($data);
            $estimate = $this->estimate;
        } else {
            $estimate = Estimate::create($data + ['number' => DocumentNumber::nextEstimate()]);
        }

        $this->saveItemsTo($estimate);
        $estimate->load('items');
        $estimate->recalculate();

        session()->flash('status', $this->estimate ? 'برآورد به‌روزرسانی شد.' : 'برآورد ایجاد شد.');

        return $this->redirectRoute('estimates.show', $estimate, navigate: true);
    }

    public function render()
    {
        return view('livewire.estimates.form', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'materials' => Material::orderBy('name')->get(['id', 'name', 'unit', 'unit_price']),
        ])->title($this->estimate ? 'Edit estimate' : 'New estimate');
    }
}
