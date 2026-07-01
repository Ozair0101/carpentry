<?php

namespace App\Livewire\Concerns;

use App\Models\Material;
use Livewire\Attributes\Computed;

/**
 * Shared repeatable line-item editor used by the Estimate and Invoice forms.
 *
 * The host component must expose public `$items` (array), `$discount` and
 * `$tax_rate` properties. Each item is an associative array:
 * [material_id, description, qty, unit, unit_price].
 */
trait ManagesLineItems
{
    public array $items = [];

    public $discount = 0;

    public $tax_rate = 0;

    public function addItem(): void
    {
        $this->items[] = [
            'material_id' => null,
            'description' => '',
            'qty' => 1,
            'unit' => '',
            'unit_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * When a material is picked, auto-fill description, unit and price.
     */
    public function updatedItems($value, $key): void
    {
        if (str_ends_with((string) $key, '.material_id')) {
            $index = (int) explode('.', $key)[0];
            $this->applyMaterial($index);
        }
    }

    protected function applyMaterial(int $index): void
    {
        $id = $this->items[$index]['material_id'] ?? null;

        if ($id && ($material = Material::find($id))) {
            $this->items[$index]['description'] = $material->name;
            $this->items[$index]['unit'] = $material->unit;
            $this->items[$index]['unit_price'] = (float) $material->unit_price;
        }
    }

    public function lineTotal(array $item): float
    {
        return round((float) ($item['qty'] ?? 0) * (float) ($item['unit_price'] ?? 0), 2);
    }

    #[Computed]
    public function totals(): array
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += $this->lineTotal($item);
        }

        $taxable = max(0, $subtotal - (float) $this->discount);
        $tax = round($taxable * ((float) $this->tax_rate / 100), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => $tax,
            'total' => round($taxable + $tax, 2),
        ];
    }

    /**
     * Validation rules for the line items.
     */
    protected function itemRules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.material_id' => 'nullable|integer|exists:materials,id',
            'discount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * Persist the current items onto a parent model's items() relation,
     * replacing any existing rows.
     */
    protected function saveItemsTo($parent): void
    {
        $parent->items()->delete();

        foreach (array_values($this->items) as $position => $item) {
            $parent->items()->create([
                'material_id' => $item['material_id'] ?: null,
                'description' => $item['description'],
                'qty' => (float) $item['qty'],
                'unit' => $item['unit'] ?: null,
                'unit_price' => (float) $item['unit_price'],
                'line_total' => $this->lineTotal($item),
                'position' => $position + 1,
            ]);
        }
    }
}
