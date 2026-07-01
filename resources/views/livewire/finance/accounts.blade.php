<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            Total cash on hand: <span class="font-bold">{{ Format::money($totalCash) }}</span>
        </div>
        <button wire:click="create" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ New account</button>
    </div>

    @if ($showForm)
        <form wire:submit="save" class="grid gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Name *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Type</label>
                <select wire:model="type" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Opening balance</label>
                <input type="number" step="0.01" wire:model="opening_balance" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div class="flex items-end gap-3">
                <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" wire:model="is_default" class="rounded border-stone-300 text-amber-600"> Default</label>
            </div>
            <div class="sm:col-span-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Save</button>
                <button type="button" wire:click="$set('showForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200">Cancel</button>
            </div>
        </form>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($accounts as $account)
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-stone-800">{{ $account->name }}
                            @if ($account->is_default)<span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-700">default</span>@endif
                        </p>
                        <p class="text-xs uppercase tracking-wide text-stone-400">{{ $account->type }}</p>
                    </div>
                    <button wire:click="edit({{ $account->id }})" class="text-sm font-medium text-stone-400 hover:text-amber-700">Edit</button>
                </div>
                <p class="mt-3 text-2xl font-bold text-stone-800">{{ Format::money($account->balance()) }}</p>
                <p class="text-xs text-stone-400">Opening {{ Format::money($account->opening_balance) }}</p>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 p-8 text-center text-stone-400 sm:col-span-2 lg:col-span-3">
                No accounts yet. Add a cash or bank account to start tracking money.
            </div>
        @endforelse
    </div>
</div>
