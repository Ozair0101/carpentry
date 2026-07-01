<?php use App\Support\Format; ?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">{{ $supplier->name }}</h2>
            @if ($supplier->company)<p class="text-stone-500">{{ $supplier->company }}</p>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bills.create', ['supplier' => $supplier->id]) }}" wire:navigate class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">+ New bill</a>
            <a href="{{ route('suppliers.edit', $supplier) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">Edit</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <div class="rounded-xl bg-red-50 p-4">
                <p class="text-sm text-red-700">Currently owed</p>
                <p class="text-2xl font-bold text-red-700">{{ Format::money($supplier->balanceOwed()) }}</p>
            </div>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-stone-400">Phone</dt><dd class="text-stone-700">{{ $supplier->phone ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Email</dt><dd class="text-stone-700">{{ $supplier->email ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Address</dt><dd class="whitespace-pre-line text-stone-700">{{ $supplier->address ?: '—' }}</dd></div>
                @if ($supplier->notes)<div><dt class="text-stone-400">Notes</dt><dd class="whitespace-pre-line text-stone-700">{{ $supplier->notes }}</dd></div>@endif
            </dl>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-2">
            <h3 class="mb-3 font-semibold text-stone-800">Bills</h3>
            <div class="divide-y divide-stone-100">
                @forelse ($supplier->purchases as $bill)
                    <a href="{{ route('bills.show', $bill) }}" wire:navigate class="flex items-center justify-between py-3 hover:text-amber-700">
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ $bill->reference ?: 'Bill #'.$bill->id }}</p>
                            <p class="text-xs text-stone-500">{{ $bill->bill_date->format('d M Y') }} @if($bill->due_date) · due {{ $bill->due_date->format('d M Y') }}@endif</p>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-stone-600">{{ Format::money($bill->total) }}</span>
                            <x-status-badge :status="$bill->status" />
                        </div>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-stone-400">No bills recorded for this supplier.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
