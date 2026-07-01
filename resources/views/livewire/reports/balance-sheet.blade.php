<?php use App\Support\Format; ?>
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Controls --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between print:hidden">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">تا تاریخ</label>
            <input type="date" wire:model.live="asOf" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <button onclick="window.print()" class="rounded-lg bg-stone-800 px-3 py-2 text-xs font-semibold text-white hover:bg-stone-900">🖨 چاپ</button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 p-6 text-center">
            <h2 class="text-xl font-bold text-stone-800">بیلانس (ترازنامه)</h2>
            <p class="mt-1 text-sm text-stone-500">به تاریخ {{ $report['asOf']->translatedFormat('d M Y') }}</p>
        </div>

        <div class="grid gap-6 p-6 sm:grid-cols-2">
            {{-- Assets --}}
            <div>
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-stone-400">دارایی‌ها</h3>
                <div class="space-y-1">
                    @foreach ($report['assets']['cashAccounts'] as $acc)
                        <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                            <span class="text-stone-600">{{ $acc['name'] }}</span>
                            <span class="font-medium text-stone-800">{{ Format::money($acc['balance']) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-600">حسابات قابل دریافت (طلب از مشتریان)</span>
                        <span class="font-medium text-stone-800">{{ Format::money($report['assets']['receivable']) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-600">مساعده‌های قابل بازیافت</span>
                        <span class="font-medium text-stone-800">{{ Format::money($report['assets']['advancesReceivable']) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-stone-200 pt-2 text-sm font-semibold">
                        <span class="text-stone-700">مجموع دارایی‌ها</span>
                        <span class="text-stone-900">{{ Format::money($report['assetsTotal']) }}</span>
                    </div>
                </div>
            </div>

            {{-- Liabilities + equity --}}
            <div>
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-stone-400">بدهی‌ها و سرمایه</h3>
                <div class="space-y-1">
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-600">حسابات قابل پرداخت (بل تأمین‌کنندگان)</span>
                        <span class="font-medium text-stone-800">{{ Format::money($report['liabilities']['payable']) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-stone-50 py-1.5 text-sm">
                        <span class="text-stone-600">معاشات قابل پرداخت</span>
                        <span class="font-medium text-stone-800">{{ Format::money($report['liabilities']['salariesPayable']) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-stone-100 pt-2 text-sm font-semibold">
                        <span class="text-stone-700">مجموع بدهی‌ها</span>
                        <span class="text-red-600">{{ Format::money($report['liabilitiesTotal']) }}</span>
                    </div>
                    <div class="mt-2 flex justify-between rounded-lg {{ $report['equity'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} px-3 py-2 text-sm font-semibold">
                        <span class="text-stone-700">سرمایه / دارایی خالص</span>
                        <span class="{{ $report['equity'] >= 0 ? 'text-green-700' : 'text-red-600' }}">{{ Format::money($report['equity']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Check line --}}
        <div class="border-t border-stone-100 bg-stone-50 px-6 py-3 text-center text-xs text-stone-500">
            دارایی‌ها ({{ Format::money($report['assetsTotal']) }}) = بدهی‌ها ({{ Format::money($report['liabilitiesTotal']) }}) + سرمایه ({{ Format::money($report['equity']) }})
        </div>
    </div>

    <p class="text-center text-xs text-stone-400 print:hidden">سرمایه به‌عنوان مابه‌التفاوت دارایی‌ها و بدهی‌ها محاسبه می‌شود (ارزش خالص کسب‌وکار).</p>
</div>
