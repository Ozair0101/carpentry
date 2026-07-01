<?php

namespace App\Livewire\Purchases;

use App\Livewire\Concerns\ManagesLineItems;
use App\Models\Material;
use App\Models\Project;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Form extends Component
{
    use ManagesLineItems;

    public ?Purchase $purchase = null;

    public $supplier_id = null;

    public $project_id = null;

    public string $reference = '';

    public string $bill_date = '';

    public ?string $due_date = null;

    public string $notes = '';

    public function mount(?Purchase $purchase = null): void
    {
        if ($purchase && $purchase->exists) {
            $this->purchase = $purchase;
            $this->supplier_id = $purchase->supplier_id;
            $this->project_id = $purchase->project_id;
            $this->reference = (string) $purchase->reference;
            $this->bill_date = $purchase->bill_date->format('Y-m-d');
            $this->due_date = $purchase->due_date?->format('Y-m-d');
            $this->notes = (string) $purchase->notes;
            $taxable = (float) $purchase->subtotal;
            $this->tax_rate = $taxable > 0 ? round((float) $purchase->tax_total / $taxable * 100, 2) : 0;
            $this->items = $purchase->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'description' => $i->description,
                'qty' => $i->qty,
                'unit' => $i->unit,
                'unit_price' => $i->unit_price,
            ])->toArray();
        } else {
            $this->supplier_id = request()->integer('supplier') ?: null;
            $this->bill_date = Carbon::today()->format('Y-m-d');
            $this->due_date = Carbon::today()->addDays(30)->format('Y-m-d');
            $this->addItem();
        }
    }

    public function save()
    {
        $this->validate(array_merge([
            'supplier_id' => 'required|exists:suppliers,id',
            'project_id' => 'nullable|exists:projects,id',
            'reference' => 'nullable|string|max:255',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date',
        ], $this->itemRules()));

        $data = [
            'supplier_id' => $this->supplier_id,
            'project_id' => $this->project_id ?: null,
            'reference' => $this->reference ?: null,
            'bill_date' => $this->bill_date,
            'due_date' => $this->due_date ?: null,
            'notes' => $this->notes ?: null,
            'tax_total' => $this->totals['tax'],
        ];

        if ($this->purchase) {
            $this->purchase->update($data);
            $purchase = $this->purchase;
        } else {
            $purchase = Purchase::create($data + ['status' => 'unpaid']);
        }

        $this->saveItemsTo($purchase);
        $purchase->load('items');
        $purchase->recalculate();

        session()->flash('status', $this->purchase ? 'بل به‌روزرسانی شد.' : 'بل ایجاد شد.');

        return $this->redirectRoute('bills.show', $purchase, navigate: true);
    }

    public function render()
    {
        return view('livewire.purchases.form', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'projects' => Project::orderBy('title')->get(['id', 'title']),
            'materials' => Material::orderBy('name')->get(['id', 'name', 'unit', 'unit_price']),
        ])->title($this->purchase ? 'ویرایش بل' : 'بل جدید');
    }
}
