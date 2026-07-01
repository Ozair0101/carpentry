<?php use App\Support\Format; ?>
{{-- Shared line-item editor. Requires $materials in scope and the ManagesLineItems trait. --}}
<div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-right text-xs font-semibold uppercase tracking-wide text-stone-400">
                <tr>
                    <th class="pb-2 pl-2">قلم / توضیحات</th>
                    <th class="pb-2 px-2 w-24">تعداد</th>
                    <th class="pb-2 px-2 w-24">واحد</th>
                    <th class="pb-2 px-2 w-32">قیمت واحد</th>
                    <th class="pb-2 px-2 w-32 text-left">مجموع</th>
                    <th class="pb-2 pr-2 w-8"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr wire:key="item-{{ $index }}" class="align-top">
                        <td class="py-1 pl-2">
                            <select wire:model.live="items.{{ $index }}.material_id" class="mb-1 w-full rounded-lg border border-stone-200 px-2 py-1.5 text-xs text-stone-500">
                                <option value="">— انتخاب از فهرست قیمت —</option>
                                @foreach ($materials as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ Format::money($m->unit_price) }}/{{ $m->unit }})</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model="items.{{ $index }}.description" placeholder="توضیحات"
                                   class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                            @error("items.{$index}.description") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </td>
                        <td class="py-1 px-2"><input type="number" step="0.01" wire:model.live="items.{{ $index }}.qty" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm"></td>
                        <td class="py-1 px-2"><input type="text" wire:model="items.{{ $index }}.unit" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm"></td>
                        <td class="py-1 px-2"><input type="number" step="0.01" wire:model.live="items.{{ $index }}.unit_price" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm"></td>
                        <td class="py-1 px-2 pt-3 text-left font-medium text-stone-700">{{ Format::money($this->lineTotal($item)) }}</td>
                        <td class="py-1 pr-2 pt-2 text-center">
                            <button type="button" wire:click="removeItem({{ $index }})" class="text-stone-400 hover:text-red-600" title="حذف">✕</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @error('items') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

    <button type="button" wire:click="addItem" class="mt-3 rounded-lg border border-dashed border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-600 hover:bg-stone-50">
        + افزودن سطر
    </button>

    {{-- Totals --}}
    <div class="mt-5 flex justify-end">
        <div class="w-full space-y-2 sm:w-72">
            <div class="flex justify-between text-sm">
                <span class="text-stone-500">جمع جزء</span>
                <span class="font-medium">{{ Format::money($this->totals['subtotal']) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-stone-500">تخفیف</span>
                <input type="number" step="0.01" wire:model.live="discount" class="w-28 rounded-lg border border-stone-300 px-2 py-1 text-left text-sm">
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-stone-500">نرخ مالیات ٪</span>
                <input type="number" step="0.01" wire:model.live="tax_rate" class="w-28 rounded-lg border border-stone-300 px-2 py-1 text-left text-sm">
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-stone-500">مالیات</span>
                <span class="font-medium">{{ Format::money($this->totals['tax']) }}</span>
            </div>
            <div class="flex justify-between border-t border-stone-200 pt-2 text-base font-bold text-stone-800">
                <span>مجموع</span>
                <span>{{ Format::money($this->totals['total']) }}</span>
            </div>
        </div>
    </div>
</div>
