<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search invoices…"
                       class="w-full rounded-lg border border-stone-300 py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                <span class="pointer-events-none absolute left-3 top-2.5 text-stone-400">🔍</span>
            </div>
            <select wire:model.live="status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                <option value="">All statuses</option>
                @foreach (\App\Models\Invoice::STATUSES as $s)<option value="{{ $s }}">{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <a href="{{ route('invoices.create') }}" wire:navigate class="rounded-lg bg-amber-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-amber-700">+ New invoice</a>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Total outstanding: <span class="font-bold">{{ Format::money($outstanding) }}</span>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Number</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Due</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($invoices as $invoice)
                    <tr class="cursor-pointer hover:bg-stone-50" onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $invoice->number }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $invoice->customer->name }}</td>
                        <td class="hidden px-4 py-3 text-stone-500 sm:table-cell">{{ $invoice->due_date?->format('d M Y') ?: '—' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$invoice->status" /></td>
                        <td class="px-4 py-3 text-right font-medium">{{ Format::money($invoice->total) }}</td>
                        <td class="px-4 py-3 text-right {{ $invoice->balance() > 0 ? 'text-red-600' : 'text-green-600' }}">{{ Format::money($invoice->balance()) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-stone-400">No invoices yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
