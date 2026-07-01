<?php use App\Support\Format; ?>
<div class="space-y-4">
    {{-- Month P&L summary --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">درآمد (این ماه)</p>
            <p class="mt-1 text-xl font-bold text-green-600">{{ Format::money($monthIncome) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">مصرف (این ماه)</p>
            <p class="mt-1 text-xl font-bold text-red-600">{{ Format::money($monthExpense) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">خالص (این ماه)</p>
            <p class="mt-1 text-xl font-bold {{ $monthIncome - $monthExpense >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ Format::money($monthIncome - $monthExpense) }}</p>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-2">
            <select wire:model.live="direction" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">همه</option>
                <option value="in">پول ورودی</option>
                <option value="out">پول خروجی</option>
            </select>
            <select wire:model.live="accountFilter" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">همه حساب‌ها</option>
                @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button wire:click="openForm('income')" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">+ درآمد</button>
            <button wire:click="openForm('expense')" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">+ مصرف</button>
            <button wire:click="openForm('transfer')" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">انتقال</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">تاریخ</th>
                    <th class="px-4 py-3">توضیحات</th>
                    <th class="hidden px-4 py-3 sm:table-cell">حساب</th>
                    <th class="hidden px-4 py-3 md:table-cell">دسته‌بندی</th>
                    <th class="px-4 py-3 text-left">مبلغ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($transactions as $tx)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 text-stone-500">{{ $tx->occurred_on->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-stone-700">
                            {{ $tx->description ?: '—' }}
                            @if ($tx->sourceable)<span class="mr-1 text-xs text-stone-400">({{ class_basename($tx->sourceable_type) }})</span>@endif
                        </td>
                        <td class="hidden px-4 py-3 text-stone-500 sm:table-cell">{{ $tx->account?->name }}</td>
                        <td class="hidden px-4 py-3 text-stone-500 md:table-cell">{{ $tx->category?->name ?: '—' }}</td>
                        <td class="px-4 py-3 text-left font-medium {{ $tx->direction === 'in' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tx->direction === 'in' ? '+' : '−' }}{{ Format::money($tx->amount) }}
                        </td>
                        <td class="px-4 py-3 text-left">
                            <button wire:click="delete({{ $tx->id }})" wire:confirm="این تراکنش حذف شود؟" class="text-stone-300 hover:text-red-600">✕</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-stone-400">هنوز تراکنشی وجود ندارد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $transactions->links() }}</div>

    {{-- Add modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="save" class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">{{ ['income' => 'درآمد جدید', 'expense' => 'مصرف جدید', 'transfer' => 'انتقال جدید'][$entryType] ?? 'مورد جدید' }}</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">{{ $entryType === 'transfer' ? 'از حساب' : 'حساب' }} *</label>
                    <select wire:model="account_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('account_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                @if ($entryType === 'transfer')
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">به حساب *</label>
                        <select wire:model="to_account_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                            <option value="">— انتخاب —</option>
                            @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                        </select>
                        @error('to_account_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">دسته‌بندی</label>
                        <select wire:model="category_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                            <option value="">— هیچ‌کدام —</option>
                            @foreach ($categories->where('kind', $entryType === 'income' ? 'income' : 'expense') as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">مبلغ *</label>
                        <input type="number" step="0.01" wire:model="amount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ *</label>
                        <input type="date" wire:model="occurred_on" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">توضیحات</label>
                    <input type="text" wire:model="description" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <x-save-button class="!px-4" />
                </div>
            </form>
        </div>
    @endif
</div>
