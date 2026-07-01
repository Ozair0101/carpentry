<?php use App\Support\Format; ?>
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-2">
            <div class="relative flex-1 sm:max-w-xs">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="جستجوی کارها…"
                       class="w-full rounded-lg border border-stone-300 py-2 pr-9 pl-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                <span class="pointer-events-none absolute right-3 top-2.5 text-stone-400">🔍</span>
            </div>
            <select wire:model.live="status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                <option value="">همه وضعیت‌ها</option>
                @foreach (\App\Models\Project::STATUSES as $s)
                    <option value="{{ $s }}">{{ \App\Support\Labels::get($s) }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('jobs.create') }}" wire:navigate class="rounded-lg bg-amber-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-amber-700">+ کار جدید</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">کار</th>
                    <th class="px-4 py-3">مشتری</th>
                    <th class="hidden px-4 py-3 sm:table-cell">تاریخ سررسید</th>
                    <th class="px-4 py-3">وضعیت</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">بودجه</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($jobs as $job)
                    <tr class="cursor-pointer hover:bg-stone-50" onclick="window.location='{{ route('jobs.show', $job) }}'">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $job->title }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $job->customer->name }}</td>
                        <td class="hidden px-4 py-3 text-stone-500 sm:table-cell">{{ $job->due_date?->translatedFormat('d M Y') ?: '—' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$job->status" /></td>
                        <td class="hidden px-4 py-3 text-left text-stone-600 md:table-cell">{{ $job->budget ? Format::money($job->budget) : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-stone-400">هنوز کاری وجود ندارد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $jobs->links() }}</div>
</div>
