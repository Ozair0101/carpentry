<?php use App\Support\Format; ?>
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Action bar --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-stone-800">{{ $invoice->number }}</h2>
            <x-status-badge :status="$invoice->status" />
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($invoice->status === 'draft')
                <button wire:click="setStatus('sent')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">علامت‌گذاری به عنوان ارسال‌شده</button>
            @endif
            @if ($invoice->balance() > 0 && $invoice->status !== 'cancelled')
                <button wire:click="openPayment" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">ثبت پرداخت</button>
            @endif
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">پی‌دی‌اف</a>
            <a href="{{ route('invoices.edit', $invoice) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">ویرایش</a>
            <button wire:click="delete" wire:confirm="این فاکتور حذف شود؟" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-400 hover:text-red-600">حذف</button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Document --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-2">
            <div class="flex justify-between border-b border-stone-100 pb-5">
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-400">صورت‌حساب برای</p>
                    <p class="mt-1 font-semibold text-stone-800">
                        <a href="{{ route('customers.show', $invoice->customer) }}" wire:navigate class="hover:text-amber-700">{{ $invoice->customer->name }}</a>
                    </p>
                    <p class="whitespace-pre-line text-sm text-stone-500">{{ $invoice->customer->billing_address }}</p>
                </div>
                <div class="text-left text-sm text-stone-500">
                    <p>تاریخ صدور: <span class="text-stone-700">{{ $invoice->issue_date->translatedFormat('d M Y') }}</span></p>
                    @if ($invoice->due_date)<p>سررسید: <span class="text-stone-700">{{ $invoice->due_date->translatedFormat('d M Y') }}</span></p>@endif
                </div>
            </div>

            <table class="mt-5 min-w-full text-sm">
                <thead class="text-right text-xs uppercase tracking-wide text-stone-400">
                    <tr><th class="pb-2">توضیحات</th><th class="pb-2 text-left">مقدار</th><th class="pb-2 text-left">قیمت واحد</th><th class="pb-2 text-left">مجموع</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="py-2 text-stone-700">{{ $item->description }}</td>
                            <td class="py-2 text-left text-stone-600">{{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }} {{ $item->unit }}</td>
                            <td class="py-2 text-left text-stone-600">{{ Format::money($item->unit_price) }}</td>
                            <td class="py-2 text-left font-medium text-stone-700">{{ Format::money($item->line_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-5 flex justify-end">
                <div class="w-64 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-stone-500">جمع جزء</span><span>{{ Format::money($invoice->subtotal) }}</span></div>
                    @if ($invoice->discount > 0)<div class="flex justify-between"><span class="text-stone-500">تخفیف</span><span>−{{ Format::money($invoice->discount) }}</span></div>@endif
                    <div class="flex justify-between"><span class="text-stone-500">مالیات ({{ rtrim(rtrim(number_format($invoice->tax_rate, 2), '0'), '.') }}%)</span><span>{{ Format::money($invoice->tax_total) }}</span></div>
                    <div class="flex justify-between border-t border-stone-200 pt-1 font-bold text-stone-800"><span>مجموع</span><span>{{ Format::money($invoice->total) }}</span></div>
                    <div class="flex justify-between text-green-600"><span>پرداخت‌شده</span><span>{{ Format::money($invoice->amount_paid) }}</span></div>
                    <div class="flex justify-between text-base font-bold {{ $invoice->balance() > 0 ? 'text-red-600' : 'text-green-600' }}"><span>مانده قابل پرداخت</span><span>{{ Format::money($invoice->balance()) }}</span></div>
                </div>
            </div>
        </div>

        {{-- Payments --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-semibold text-stone-800">پرداخت‌ها</h3>
                @forelse ($invoice->payments as $payment)
                    <div class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ Format::money($payment->amount) }}</p>
                            <p class="text-xs text-stone-500">{{ $payment->paid_on->translatedFormat('d M Y') }} · {{ \App\Support\Labels::get($payment->method) }}</p>
                        </div>
                        <button wire:click="deletePayment({{ $payment->id }})" wire:confirm="این پرداخت حذف شود؟" class="text-stone-300 hover:text-red-600">✕</button>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-stone-400">هیچ پرداختی ثبت نشده است.</p>
                @endforelse
            </div>

            @if ($invoice->project)
                <a href="{{ route('jobs.show', $invoice->project) }}" wire:navigate class="block rounded-2xl border border-stone-200 bg-white p-4 text-sm text-stone-600 shadow-sm hover:text-amber-700">
                    کار مرتبط: {{ $invoice->project->title }} ←
                </a>
            @endif
        </div>
    </div>

    {{-- Payment modal --}}
    @if ($showPayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="recordPayment" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">ثبت پرداخت</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">مبلغ</label>
                    <input type="number" step="0.01" wire:model="payAmount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('payAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ</label>
                    <input type="date" wire:model="payDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">روش</label>
                    <select wire:model="payMethod" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        @foreach (\App\Models\Payment::METHODS as $m)<option value="{{ $m }}">{{ \App\Support\Labels::get($m) }}</option>@endforeach
                    </select>
                </div>
                @if ($accounts->isNotEmpty())
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">واریز به حساب</label>
                        <select wire:model="payAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                            <option value="">— هیچ‌کدام —</option>
                            @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">مرجع (اختیاری)</label>
                    <input type="text" wire:model="payReference" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showPayment', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">ذخیره پرداخت</button>
                </div>
            </form>
        </div>
    @endif
</div>
