<?php use App\Support\Format; ?>
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Controls --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between print:hidden">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-500">از تاریخ</label>
                <input type="date" wire:model.live="from" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-500">تا تاریخ</label>
                <input type="date" wire:model.live="to" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="preset('this_month')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-xs font-medium text-stone-600 hover:bg-stone-50">این ماه</button>
            <button wire:click="preset('last_month')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-xs font-medium text-stone-600 hover:bg-stone-50">ماه گذشته</button>
            <button wire:click="preset('this_year')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-xs font-medium text-stone-600 hover:bg-stone-50">امسال</button>
            <button onclick="window.print()" class="rounded-lg bg-stone-800 px-3 py-2 text-xs font-semibold text-white hover:bg-stone-900">🖨 چاپ</button>
        </div>
    </div>

    {{-- Report --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 p-6 text-center">
            <h2 class="text-xl font-bold text-stone-800">صورت سود و زیان</h2>
            <p class="mt-1 text-sm text-stone-500">
                {{ $report['from']->translatedFormat('d M Y') }} تا {{ $report['to']->translatedFormat('d M Y') }}
                <span class="text-stone-400">· بر مبنای نقدی</span>
            </p>
        </div>

        <div class="p-6">
            {{-- Revenue --}}
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-stone-400">عواید</h3>
            <div class="mb-5 space-y-1">
                @forelse ($report['revenue'] as $label => $amount)
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-700">{{ $label }}</span>
                        <span class="font-medium text-stone-800">{{ Format::money($amount) }}</span>
                    </div>
                @empty
                    <p class="py-2 text-sm text-stone-400">هیچ عوایدی در این دوره ثبت نشده است.</p>
                @endforelse
                <div class="flex justify-between border-t border-stone-200 pt-2 text-sm font-semibold">
                    <span class="text-stone-600">مجموع عواید</span>
                    <span class="text-green-600">{{ Format::money($report['revenueTotal']) }}</span>
                </div>
            </div>

            {{-- Expenses --}}
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-stone-400">مصارف</h3>
            <div class="mb-5 space-y-1">
                @forelse ($report['expense'] as $label => $amount)
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-700">{{ $label }}</span>
                        <span class="font-medium text-stone-800">{{ Format::money($amount) }}</span>
                    </div>
                @empty
                    <p class="py-2 text-sm text-stone-400">هیچ مصرفی در این دوره ثبت نشده است.</p>
                @endforelse
                <div class="flex justify-between border-t border-stone-200 pt-2 text-sm font-semibold">
                    <span class="text-stone-600">مجموع مصارف</span>
                    <span class="text-red-600">{{ Format::money($report['expenseTotal']) }}</span>
                </div>
            </div>

            {{-- Net --}}
            <div class="flex items-center justify-between rounded-xl {{ $report['net'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} px-5 py-4">
                <span class="text-base font-bold text-stone-800">مفاد / زیان خالص</span>
                <span class="text-xl font-extrabold {{ $report['net'] >= 0 ? 'text-green-700' : 'text-red-600' }}">{{ Format::money($report['net']) }}</span>
            </div>
        </div>
    </div>

    <p class="text-center text-xs text-stone-400 print:hidden">این گزارش بر اساس پول واقعی جابجا‌شده در دفتر کل تهیه شده است (نه مبلغ فاکتورشده).</p>
</div>
