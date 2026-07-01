<?php use App\Support\Format; ?>
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-stone-800">{{ $purchase->reference ?: 'بل #'.$purchase->id }}</h2>
            <x-status-badge :status="$purchase->status" />
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($purchase->balance() > 0 && $purchase->status !== 'cancelled')
                <button wire:click="openPayment" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">پرداخت به تأمین‌کننده</button>
            @endif
            <a href="{{ route('bills.edit', $purchase) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">ویرایش</a>
            <button wire:click="delete" wire:confirm="این بل حذف شود؟" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-400 hover:text-red-600">حذف</button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-2">
            <div class="flex justify-between border-b border-stone-100 pb-5">
                <div>
                    <p class="text-xs uppercase tracking-wide text-stone-400">تأمین‌کننده</p>
                    <p class="mt-1 font-semibold text-stone-800">
                        <a href="{{ route('suppliers.show', $purchase->supplier) }}" wire:navigate class="hover:text-amber-700">{{ $purchase->supplier->name }}</a>
                    </p>
                </div>
                <div class="text-left text-sm text-stone-500">
                    <p>تاریخ بل: <span class="text-stone-700">{{ $purchase->bill_date->translatedFormat('d M Y') }}</span></p>
                    @if ($purchase->due_date)<p>سررسید: <span class="text-stone-700">{{ $purchase->due_date->translatedFormat('d M Y') }}</span></p>@endif
                    @if ($purchase->project)<p>پروژه: <a href="{{ route('jobs.show', $purchase->project) }}" wire:navigate class="text-amber-700">{{ $purchase->project->title }}</a></p>@endif
                </div>
            </div>

            <table class="mt-5 min-w-full text-sm">
                <thead class="text-right text-xs uppercase tracking-wide text-stone-400">
                    <tr><th class="pb-2">توضیحات</th><th class="pb-2 text-left">مقدار</th><th class="pb-2 text-left">قیمت واحد</th><th class="pb-2 text-left">مجموع</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($purchase->items as $item)
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
                    <div class="flex justify-between"><span class="text-stone-500">جمع جزء</span><span>{{ Format::money($purchase->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-stone-500">مالیات</span><span>{{ Format::money($purchase->tax_total) }}</span></div>
                    <div class="flex justify-between border-t border-stone-200 pt-1 font-bold text-stone-800"><span>مجموع</span><span>{{ Format::money($purchase->total) }}</span></div>
                    <div class="flex justify-between text-green-600"><span>پرداخت‌شده</span><span>{{ Format::money($purchase->amountPaid()) }}</span></div>
                    <div class="flex justify-between text-base font-bold {{ $purchase->balance() > 0 ? 'text-red-600' : 'text-green-600' }}"><span>مانده</span><span>{{ Format::money($purchase->balance()) }}</span></div>
                </div>
            </div>
            @if ($purchase->notes)<p class="mt-4 border-t border-stone-100 pt-3 text-sm text-stone-500">{{ $purchase->notes }}</p>@endif
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h3 class="mb-3 font-semibold text-stone-800">پرداخت‌ها</h3>
            @forelse ($purchase->payments as $payment)
                <div class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ Format::money($payment->amount) }}</p>
                        <p class="text-xs text-stone-500">{{ $payment->occurred_on->translatedFormat('d M Y') }} · {{ $payment->account?->name }}</p>
                    </div>
                    <button wire:click="deletePayment({{ $payment->id }})" wire:confirm="این پرداخت حذف شود؟" class="text-stone-300 hover:text-red-600">✕</button>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">هنوز پرداختی وجود ندارد.</p>
            @endforelse
        </div>
    </div>

    @if ($showPayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="recordPayment" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">پرداخت به تأمین‌کننده</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">مبلغ</label>
                    <input type="number" step="0.01" wire:model="payAmount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('payAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">پرداخت از حساب</label>
                    <select wire:model="payAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('payAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ</label>
                    <input type="date" wire:model="payDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
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
