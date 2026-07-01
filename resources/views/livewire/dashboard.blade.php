<?php use App\Support\Format; ?>
<div class="space-y-6">
    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <a href="{{ route('jobs.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">کارهای فعال</p>
            <p class="mt-2 text-3xl font-bold text-stone-800">{{ $activeJobs }}</p>
        </a>
        <a href="{{ route('estimates.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">برآوردهای باز</p>
            <p class="mt-2 text-3xl font-bold text-stone-800">{{ $openEstimatesCount }}</p>
        </a>
        <a href="{{ route('invoices.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">مانده بدهی</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ Format::money($outstanding) }}</p>
            <p class="text-xs text-stone-400">{{ $unpaidCount }} فاکتور پرداخت‌نشده</p>
        </a>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">پرداخت‌شده در این ماه</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ Format::money($revenueThisMonth) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Jobs due this week --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h2 class="mb-3 font-semibold text-stone-800">کارهای موعددار این هفته</h2>
            <div class="space-y-2">
                @forelse ($jobsDueThisWeek as $job)
                    <a href="{{ route('jobs.show', $job) }}" wire:navigate class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-stone-50">
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ $job->title }}</p>
                            <p class="text-xs text-stone-500">{{ $job->customer->name }}</p>
                        </div>
                        <span class="text-xs font-medium text-amber-700">{{ $job->due_date?->translatedFormat('D d M') }}</span>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-stone-400">این هفته موعدی وجود ندارد 🎉</p>
                @endforelse
            </div>
        </div>

        {{-- Open estimates --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h2 class="mb-3 font-semibold text-stone-800">برآوردهای باز</h2>
            <div class="space-y-2">
                @forelse ($openEstimates as $estimate)
                    <a href="{{ route('estimates.show', $estimate) }}" wire:navigate class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-stone-50">
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ $estimate->number }}</p>
                            <p class="text-xs text-stone-500">{{ $estimate->customer->name }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium">{{ Format::money($estimate->total) }}</p>
                            <x-status-badge :status="$estimate->status" />
                        </div>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-stone-400">برآورد بازی وجود ندارد</p>
                @endforelse
            </div>
        </div>

        {{-- Unpaid invoices --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h2 class="mb-3 font-semibold text-stone-800">فاکتورهای پرداخت‌نشده</h2>
            <div class="space-y-2">
                @forelse ($unpaidInvoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-stone-50">
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ $invoice->number }}</p>
                            <p class="text-xs text-stone-500">{{ $invoice->customer->name }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-red-600">{{ Format::money($invoice->balance()) }}</p>
                            <x-status-badge :status="$invoice->status" />
                        </div>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-stone-400">همه فاکتورها پرداخت شده‌اند 🎉</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
