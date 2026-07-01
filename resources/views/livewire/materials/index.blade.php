<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative sm:w-80">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search materials…"
                   class="w-full rounded-lg border border-stone-300 py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:ring-amber-500">
            <span class="pointer-events-none absolute left-3 top-2.5 text-stone-400">🔍</span>
        </div>
        <button wire:click="create" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ New material</button>
    </div>

    @if ($showForm)
        <form wire:submit="save" class="grid gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:grid-cols-5">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-stone-600">Name *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Unit</label>
                <input type="text" wire:model="unit" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Unit price *</label>
                <input type="number" step="0.01" wire:model="unit_price" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                @error('unit_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Category</label>
                <input type="text" wire:model="category" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div class="flex items-end gap-2 sm:col-span-5">
                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Save</button>
                <button type="button" wire:click="$set('showForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200">Cancel</button>
            </div>
        </form>
    @endif

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Material</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Category</th>
                    <th class="px-4 py-3">Unit</th>
                    <th class="px-4 py-3 text-right">Price</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($materials as $material)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $material->name }}</td>
                        <td class="hidden px-4 py-3 text-stone-500 sm:table-cell">{{ $material->category ?: '—' }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $material->unit }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ Format::money($material->unit_price) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $material->id }})" class="text-sm font-medium text-stone-500 hover:text-amber-700">Edit</button>
                            <button wire:click="delete({{ $material->id }})" wire:confirm="Delete this material?" class="ml-3 text-sm font-medium text-stone-400 hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-stone-400">No materials yet. Add items you use often for faster estimates.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $materials->links() }}</div>
</div>
