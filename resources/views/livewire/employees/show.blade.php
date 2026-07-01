<?php use App\Support\Format; ?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">{{ $employee->name }}</h2>
            <p class="text-stone-500">{{ $employee->role }} · {{ Format::money($employee->salary_rate) }} / {{ $employee->salary_type }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openPayroll" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">اجرای لیست حقوق</button>
            <button wire:click="openAdvance" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">پرداخت مساعده</button>
            <a href="{{ route('employees.edit', $employee) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">ویرایش</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">مساعده قابل بازپرداخت</p>
            <p class="mt-1 text-xl font-bold {{ $employee->advanceBalance() > 0 ? 'text-amber-700' : 'text-stone-800' }}">{{ Format::money($employee->advanceBalance()) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">معاش پرداخت‌نشده</p>
            <p class="mt-1 text-xl font-bold {{ $employee->unpaidPayroll() > 0 ? 'text-red-600' : 'text-stone-800' }}">{{ Format::money($employee->unpaidPayroll()) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">تاریخ استخدام</p>
            <p class="mt-1 text-xl font-bold text-stone-800">{{ $employee->joined_on?->translatedFormat('M Y') ?: '—' }}</p>
        </div>
    </div>

    {{-- Payroll history --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-3 font-semibold text-stone-800">تاریخچه لیست حقوق</div>
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr><th class="px-4 py-3">دوره</th><th class="px-4 py-3 text-left">پایه</th><th class="px-4 py-3 text-left">پاداش</th><th class="px-4 py-3 text-left">کسورات</th><th class="px-4 py-3 text-left">خالص</th><th class="px-4 py-3">وضعیت</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->payrolls as $payroll)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $payroll->period_label }}</td>
                        <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($payroll->base_amount) }}</td>
                        <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($payroll->bonus) }}</td>
                        <td class="px-4 py-3 text-left text-stone-600">{{ Format::money((float) $payroll->deductions + (float) $payroll->advance_deducted) }}</td>
                        <td class="px-4 py-3 text-left font-medium">{{ Format::money($payroll->net_amount) }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$payroll->status" /></td>
                        <td class="px-4 py-3 text-left">
                            @if ($payroll->status !== 'paid')
                                <button wire:click="openPay({{ $payroll->id }})" class="text-sm font-medium text-green-600 hover:text-green-800">پرداخت</button>
                            @endif
                            <button wire:click="deletePayroll({{ $payroll->id }})" wire:confirm="این اجرای لیست حقوق حذف شود؟" class="mr-2 text-stone-300 hover:text-red-600">✕</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-stone-400">هنوز لیست حقوقی اجرا نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Advances --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-3 font-semibold text-stone-800">مساعده‌ها / قرض‌ها</div>
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr><th class="px-4 py-3">تاریخ</th><th class="px-4 py-3">یادداشت</th><th class="px-4 py-3 text-left">مبلغ</th><th class="px-4 py-3 text-left">بازیافت‌شده</th><th class="px-4 py-3 text-left">باقی‌مانده</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->advances as $advance)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 text-stone-500">{{ $advance->advanced_on->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-stone-700">{{ $advance->note ?: '—' }}</td>
                        <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->amount) }}</td>
                        <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->recovered) }}</td>
                        <td class="px-4 py-3 text-left font-medium {{ $advance->outstanding() > 0 ? 'text-amber-700' : 'text-green-600' }}">{{ Format::money($advance->outstanding()) }}</td>
                        <td class="px-4 py-3 text-left"><button wire:click="deleteAdvance({{ $advance->id }})" wire:confirm="این مساعده حذف شود؟" class="text-stone-300 hover:text-red-600">✕</button></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-400">هیچ مساعده‌ای پرداخت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payroll run modal --}}
    @if ($showPayrollForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="savePayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">اجرای لیست حقوق</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">دوره</label>
                    <input type="text" wire:model="periodLabel" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('periodLabel') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">پایه</label><input type="number" step="0.01" wire:model="payrollBase" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">پاداش</label><input type="number" step="0.01" wire:model="payrollBonus" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">کسورات</label><input type="number" step="0.01" wire:model="payrollDeductions" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">بازیافت مساعده</label><input type="number" step="0.01" wire:model="payrollAdvance" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                </div>
                <p class="text-xs text-stone-500">مساعده باقی‌مانده: {{ Format::money($employee->advanceBalance()) }}</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showPayrollForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">ایجاد اجرا</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Pay payroll modal --}}
    @if ($payingPayrollId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="payPayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">پرداخت معاش</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">پرداخت از حساب</label>
                    <select wire:model="payAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب کنید —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('payAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ</label>
                    <input type="date" wire:model="payDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('payingPayrollId', null)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">پرداخت</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Advance modal --}}
    @if ($showAdvanceForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="saveAdvance" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">پرداخت مساعده</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">مبلغ</label>
                    <input type="number" step="0.01" wire:model="advAmount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('advAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">پرداخت از حساب</label>
                    <select wire:model="advAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب کنید —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('advAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ</label>
                    <input type="date" wire:model="advDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">یادداشت</label>
                    <input type="text" wire:model="advNote" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showAdvanceForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">پرداخت مساعده</button>
                </div>
            </form>
        </div>
    @endif
</div>
