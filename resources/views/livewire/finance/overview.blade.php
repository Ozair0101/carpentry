<?php use App\Support\Format; ?>
<div class="space-y-6">
    {{-- Top KPIs --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <a href="{{ route('accounts.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">نقده موجود</p>
            <p class="mt-2 text-2xl font-bold text-stone-800">{{ Format::money($totalCash) }}</p>
        </a>
        <a href="{{ route('invoices.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">قابل دریافت (طلب ما)</p>
            <p class="mt-2 text-2xl font-bold text-green-600">{{ Format::money($receivable) }}</p>
        </a>
        <a href="{{ route('bills.index') }}" wire:navigate class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm transition hover:shadow">
            <p class="text-sm text-stone-500">قابل پرداخت (بدهی ما)</p>
            <p class="mt-2 text-2xl font-bold text-red-600">{{ Format::money($billsPayable + $payrollPayable) }}</p>
            <p class="text-xs text-stone-400">بل‌ها {{ Format::money($billsPayable) }} · حقوق {{ Format::money($payrollPayable) }}</p>
        </a>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">پیش‌پرداخت‌های داده‌شده</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ Format::money($advancesOut) }}</p>
        </div>
    </div>

    {{-- This-month P&L --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">درآمد این ماه</p>
            <p class="mt-1 text-xl font-bold text-green-600">{{ Format::money($income) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">مصارف این ماه</p>
            <p class="mt-1 text-xl font-bold text-red-600">{{ Format::money($expense) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">مفاد خالص این ماه</p>
            <p class="mt-1 text-xl font-bold {{ $income - $expense >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ Format::money($income - $expense) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Accounts --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">حساب‌ها</h2>
                <a href="{{ route('transactions.index') }}" wire:navigate class="text-sm text-amber-700 hover:underline">دفتر کل ←</a>
            </div>
            @forelse ($accounts as $account)
                <div class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0">
                    <span class="text-sm text-stone-700">{{ $account->name }}</span>
                    <span class="text-sm font-medium">{{ Format::money($account->balance()) }}</span>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400"><a href="{{ route('accounts.index') }}" wire:navigate class="text-amber-700">افزودن حساب ←</a></p>
            @endforelse
        </div>

        {{-- Bills to pay --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-semibold text-stone-800">بل‌های قابل پرداخت</h2>
            @forelse ($billsDue as $bill)
                <a href="{{ route('bills.show', $bill) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $bill->supplier->name }}</p>
                        <p class="text-xs {{ $bill->due_date && $bill->due_date->isPast() ? 'text-red-600' : 'text-stone-500' }}">سررسید {{ $bill->due_date?->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="text-sm font-medium text-red-600">{{ Format::money($bill->balance()) }}</span>
                </a>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">چیزی برای پرداخت نیست 🎉</p>
            @endforelse
        </div>

        {{-- Money owed to us --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-semibold text-stone-800">چه کسانی باید به ما بپردازند</h2>
            @forelse ($invoicesDue as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $invoice->customer->name }}</p>
                        <p class="text-xs {{ $invoice->due_date && $invoice->due_date->isPast() ? 'text-red-600' : 'text-stone-500' }}">{{ $invoice->number }} · سررسید {{ $invoice->due_date?->translatedFormat('d M Y') ?: '—' }}</p>
                    </div>
                    <span class="text-sm font-medium text-green-600">{{ Format::money($invoice->balance()) }}</span>
                </a>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">همه تسویه شده 🎉</p>
            @endforelse
        </div>
    </div>
</div>
