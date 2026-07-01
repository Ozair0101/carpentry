<?php use App\Support\Format; ?>
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Action bar --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-stone-800">{{ $estimate->number }}</h2>
            <x-status-badge :status="$estimate->status" />
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($estimate->status === 'draft')
                <button wire:click="setStatus('sent')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">علامت‌گذاری به عنوان ارسال‌شده</button>
            @endif
            @if (in_array($estimate->status, ['draft', 'sent']))
                <button wire:click="setStatus('approved')" class="rounded-lg border border-green-300 bg-white px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-50">تأیید</button>
                <button wire:click="setStatus('rejected')" class="rounded-lg border border-red-300 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">رد</button>
            @endif
            @if ($estimate->project)
                <a href="{{ route('jobs.show', $estimate->project) }}" wire:navigate class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">مشاهده کار →</a>
            @else
                <button wire:click="convertToJob" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">تبدیل به کار</button>
            @endif
            <a href="{{ route('estimates.pdf', $estimate) }}" target="_blank" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">PDF</a>
            <a href="{{ route('estimates.edit', $estimate) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">ویرایش</a>
            <button wire:click="delete" wire:confirm="این برآورد حذف شود؟" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-400 hover:text-red-600">حذف</button>
        </div>
    </div>

    {{-- Document --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex justify-between border-b border-stone-100 pb-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-stone-400">صورتحساب برای</p>
                <p class="mt-1 font-semibold text-stone-800">
                    <a href="{{ route('customers.show', $estimate->customer) }}" wire:navigate class="hover:text-amber-700">{{ $estimate->customer->name }}</a>
                </p>
                @if ($estimate->customer->company)<p class="text-sm text-stone-500">{{ $estimate->customer->company }}</p>@endif
                <p class="whitespace-pre-line text-sm text-stone-500">{{ $estimate->customer->billing_address }}</p>
            </div>
            <div class="text-left text-sm text-stone-500">
                <p>تاریخ صدور: <span class="text-stone-700">{{ $estimate->issue_date->translatedFormat('d M Y') }}</span></p>
                @if ($estimate->valid_until)<p>معتبر تا: <span class="text-stone-700">{{ $estimate->valid_until->translatedFormat('d M Y') }}</span></p>@endif
            </div>
        </div>

        <table class="mt-5 min-w-full text-sm">
            <thead class="text-right text-xs uppercase tracking-wide text-stone-400">
                <tr><th class="pb-2">توضیحات</th><th class="pb-2 text-left">مقدار</th><th class="pb-2 text-left">قیمت واحد</th><th class="pb-2 text-left">مجموع</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($estimate->items as $item)
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
                <div class="flex justify-between"><span class="text-stone-500">جمع جزء</span><span>{{ Format::money($estimate->subtotal) }}</span></div>
                @if ($estimate->discount > 0)<div class="flex justify-between"><span class="text-stone-500">تخفیف</span><span>−{{ Format::money($estimate->discount) }}</span></div>@endif
                <div class="flex justify-between"><span class="text-stone-500">مالیات ({{ rtrim(rtrim(number_format($estimate->tax_rate, 2), '0'), '.') }}%)</span><span>{{ Format::money($estimate->tax_total) }}</span></div>
                <div class="flex justify-between border-t border-stone-200 pt-1 text-base font-bold text-stone-800"><span>مجموع</span><span>{{ Format::money($estimate->total) }}</span></div>
            </div>
        </div>

        @if ($estimate->notes || $estimate->terms)
            <div class="mt-6 space-y-3 border-t border-stone-100 pt-4 text-sm text-stone-500">
                @if ($estimate->notes)<div><span class="font-medium text-stone-600">یادداشت‌ها:</span> {{ $estimate->notes }}</div>@endif
                @if ($estimate->terms)<div><span class="font-medium text-stone-600">شرایط:</span> {{ $estimate->terms }}</div>@endif
            </div>
        @endif
    </div>
</div>
