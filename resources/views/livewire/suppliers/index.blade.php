<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative sm:w-80">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search suppliers…"
                   class="w-full rounded-lg border border-stone-300 py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:ring-amber-500">
            <span class="pointer-events-none absolute left-3 top-2.5 text-stone-400">🔍</span>
        </div>
        <a href="{{ route('suppliers.create') }}" wire:navigate class="rounded-lg bg-amber-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-amber-700">+ New supplier</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Contact</th>
                    <th class="px-4 py-3 text-right">Owed to them</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($suppliers as $supplier)
                    <tr class="cursor-pointer hover:bg-stone-50" onclick="window.location='{{ route('suppliers.show', $supplier) }}'">
                        <td class="px-4 py-3">
                            <span class="font-medium text-stone-800">{{ $supplier->name }}</span>
                            @if ($supplier->company)<p class="text-xs text-stone-500">{{ $supplier->company }}</p>@endif
                        </td>
                        <td class="hidden px-4 py-3 text-stone-600 sm:table-cell">{{ $supplier->phone }}<div class="text-xs text-stone-400">{{ $supplier->email }}</div></td>
                        <td class="px-4 py-3 text-right font-medium {{ $supplier->balanceOwed() > 0 ? 'text-red-600' : 'text-stone-500' }}">{{ Format::money($supplier->balanceOwed()) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-12 text-center text-stone-400">No suppliers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $suppliers->links() }}</div>
</div>
