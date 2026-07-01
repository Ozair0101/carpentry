<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 items-center gap-3">
            <div class="relative flex-1 sm:max-w-xs">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search employees…"
                       class="w-full rounded-lg border border-stone-300 py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                <span class="pointer-events-none absolute left-3 top-2.5 text-stone-400">🔍</span>
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" wire:model.live="onlyActive" class="rounded border-stone-300 text-amber-600"> Active only</label>
        </div>
        <a href="{{ route('employees.create') }}" wire:navigate class="rounded-lg bg-amber-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-amber-700">+ New employee</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Role</th>
                    <th class="px-4 py-3">Salary</th>
                    <th class="px-4 py-3 text-right">Advance owed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employees as $employee)
                    <tr class="cursor-pointer hover:bg-stone-50" onclick="window.location='{{ route('employees.show', $employee) }}'">
                        <td class="px-4 py-3">
                            <span class="font-medium text-stone-800">{{ $employee->name }}</span>
                            @unless ($employee->is_active)<span class="ml-1 rounded bg-stone-100 px-1.5 py-0.5 text-xs text-stone-500">inactive</span>@endunless
                        </td>
                        <td class="hidden px-4 py-3 text-stone-600 sm:table-cell">{{ $employee->role ?: '—' }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ Format::money($employee->salary_rate) }} <span class="text-xs text-stone-400">/ {{ $employee->salary_type }}</span></td>
                        <td class="px-4 py-3 text-right {{ $employee->advanceBalance() > 0 ? 'text-amber-700' : 'text-stone-400' }}">{{ Format::money($employee->advanceBalance()) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-stone-400">No employees yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $employees->links() }}</div>
</div>
