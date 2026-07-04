<?php use App\Support\Format; ?>
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Controls --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between print:hidden">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">تا تاریخ</label>
            <input type="date" wire:model.live="asOf" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <button onclick="window.print()" class="rounded-lg bg-stone-800 px-3 py-2 text-xs font-semibold text-white hover:bg-stone-900">🖨 چاپ</button>
    </div>

    {{-- Title --}}
    <div class="text-center">
        <h2 class="text-2xl font-bold text-stone-800">بیلانس (ترازنامه)</h2>
        <p class="mt-1 text-sm text-stone-500">
            وضعیت مالی کسب‌وکار شما به تاریخ {{ $report['asOf']->translatedFormat('d M Y') }}
        </p>
    </div>

    {{-- The big idea: what you own − what you owe = what the business is worth --}}
    @php $equityPositive = $report['equity'] >= 0; @endphp
    <div class="grid items-stretch gap-3 sm:grid-cols-[1fr_auto_1fr_auto_1fr]">
        {{-- Assets --}}
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-center">
            <p class="text-xs font-semibold text-emerald-700">آنچه دارید</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ Format::money($report['assetsTotal']) }}</p>
            <p class="mt-1 text-xs text-emerald-600/80">مجموع دارایی‌ها</p>
        </div>

        <div class="flex items-center justify-center text-3xl font-light text-stone-400">−</div>

        {{-- Liabilities --}}
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-center">
            <p class="text-xs font-semibold text-red-700">آنچه بدهکارید</p>
            <p class="mt-2 text-2xl font-bold text-red-600">{{ Format::money($report['liabilitiesTotal']) }}</p>
            <p class="mt-1 text-xs text-red-500/80">مجموع بدهی‌ها</p>
        </div>

        <div class="flex items-center justify-center text-3xl font-light text-stone-400">=</div>

        {{-- Equity / net worth --}}
        <div class="rounded-2xl border-2 p-5 text-center {{ $equityPositive ? 'border-amber-300 bg-amber-50' : 'border-red-300 bg-red-50' }}">
            <p class="text-xs font-semibold {{ $equityPositive ? 'text-amber-700' : 'text-red-700' }}">ارزش خالص کسب‌وکار</p>
            <p class="mt-2 text-2xl font-extrabold {{ $equityPositive ? 'text-amber-700' : 'text-red-600' }}">{{ Format::money($report['equity']) }}</p>
            <p class="mt-1 text-xs {{ $equityPositive ? 'text-amber-600/80' : 'text-red-500/80' }}">سرمایه شما</p>
        </div>
    </div>

    {{-- Plain-language takeaway --}}
    <div class="rounded-xl border {{ $equityPositive ? 'border-emerald-100 bg-emerald-50/60' : 'border-red-100 bg-red-50/60' }} px-4 py-3 text-center text-sm text-stone-600">
        @if ($equityPositive)
            پس از پرداخت همهٔ بدهی‌ها، ارزش واقعی کسب‌وکار شما
            <span class="font-bold text-emerald-700">{{ Format::money($report['equity']) }}</span>
            است.
        @else
            بدهی‌های شما از دارایی‌هایتان بیشتر است؛ کسب‌وکار در حال حاضر
            <span class="font-bold text-red-600">{{ Format::money(abs($report['equity'])) }}</span>
            کسری دارد.
        @endif
    </div>

    {{-- Detailed breakdown --}}
    <div class="grid gap-4 sm:grid-cols-2">
        {{-- What you own --}}
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-stone-100 bg-emerald-50/50 px-5 py-3">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">+</span>
                <h3 class="text-sm font-bold text-stone-700">آنچه دارید (دارایی‌ها)</h3>
            </div>
            <div class="divide-y divide-stone-100 px-5 py-2">
                {{-- Cash & bank --}}
                <div class="py-2.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-stone-700">نقد و بانک</span>
                        <span class="font-semibold text-stone-800">{{ Format::money($report['assets']['cashTotal']) }}</span>
                    </div>
                    <p class="text-xs text-stone-400">پول موجود در حساب‌ها و صندوق</p>
                    @if ($report['assets']['cashAccounts']->count())
                        <div class="mt-2 space-y-1 border-r-2 border-stone-100 pr-3">
                            @foreach ($report['assets']['cashAccounts'] as $acc)
                                <div class="flex items-center justify-between text-xs text-stone-500">
                                    <span>{{ $acc['name'] }}</span>
                                    <span>{{ Format::money($acc['balance']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Receivable --}}
                <div class="flex items-center justify-between py-2.5 text-sm">
                    <div>
                        <span class="font-medium text-stone-700">طلب از مشتریان</span>
                        <p class="text-xs text-stone-400">فاکتورهایی که مشتریان هنوز نپرداخته‌اند</p>
                    </div>
                    <span class="font-semibold text-stone-800">{{ Format::money($report['assets']['receivable']) }}</span>
                </div>

                {{-- Advances receivable --}}
                <div class="flex items-center justify-between py-2.5 text-sm">
                    <div>
                        <span class="font-medium text-stone-700">مساعدهٔ کارمندان</span>
                        <p class="text-xs text-stone-400">پیش‌پرداختی که کارمندان باید برگردانند</p>
                    </div>
                    <span class="font-semibold text-stone-800">{{ Format::money($report['assets']['advancesReceivable']) }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between border-t-2 border-emerald-100 bg-emerald-50/50 px-5 py-3 text-sm font-bold">
                <span class="text-stone-700">مجموع دارایی‌ها</span>
                <span class="text-emerald-700">{{ Format::money($report['assetsTotal']) }}</span>
            </div>
        </div>

        {{-- What you owe --}}
        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-stone-100 bg-red-50/50 px-5 py-3">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-700">−</span>
                <h3 class="text-sm font-bold text-stone-700">آنچه بدهکارید (بدهی‌ها)</h3>
            </div>
            <div class="divide-y divide-stone-100 px-5 py-2">
                {{-- Payable --}}
                <div class="flex items-center justify-between py-2.5 text-sm">
                    <div>
                        <span class="font-medium text-stone-700">بدهی به تأمین‌کنندگان</span>
                        <p class="text-xs text-stone-400">بل‌های خرید که هنوز نپرداخته‌اید</p>
                    </div>
                    <span class="font-semibold text-stone-800">{{ Format::money($report['liabilities']['payable']) }}</span>
                </div>

                {{-- Salaries payable --}}
                <div class="flex items-center justify-between py-2.5 text-sm">
                    <div>
                        <span class="font-medium text-stone-700">معاش پرداخت‌نشده</span>
                        <p class="text-xs text-stone-400">حقوق کارمندان که هنوز پرداخت نشده</p>
                    </div>
                    <span class="font-semibold text-stone-800">{{ Format::money($report['liabilities']['salariesPayable']) }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between border-t-2 border-red-100 bg-red-50/50 px-5 py-3 text-sm font-bold">
                <span class="text-stone-700">مجموع بدهی‌ها</span>
                <span class="text-red-600">{{ Format::money($report['liabilitiesTotal']) }}</span>
            </div>

            {{-- Net worth restated inside the owe column for a clean close --}}
            <div class="flex items-center justify-between px-5 py-3 text-sm font-bold {{ $equityPositive ? 'bg-amber-50' : 'bg-red-50' }}">
                <span class="text-stone-700">ارزش خالص (سرمایه)</span>
                <span class="{{ $equityPositive ? 'text-amber-700' : 'text-red-600' }}">{{ Format::money($report['equity']) }}</span>
            </div>
        </div>
    </div>

    {{-- Balance check, stated plainly --}}
    <p class="text-center text-xs text-stone-400">
        توازن: دارایی‌ها ({{ Format::money($report['assetsTotal']) }})
        = بدهی‌ها ({{ Format::money($report['liabilitiesTotal']) }})
        + سرمایه ({{ Format::money($report['equity']) }})
    </p>
</div>
