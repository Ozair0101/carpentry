<?php use App\Support\Format; ?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">{{ $employee->name }}</h2>
            <p class="text-stone-500">{{ $employee->role }} · {{ Format::money($employee->salary_rate) }} / {{ $employee->salary_type }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('payroll.index') }}" wire:navigate class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">اجرا و پرداخت در لیست حقوق ←</a>
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

    {{-- Payroll history (read-only — manage from the payroll desk) --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-3">
            <span class="font-semibold text-stone-800">تاریخچه لیست حقوق</span>
            <a href="{{ route('payroll.index') }}" wire:navigate class="text-sm text-amber-700 hover:underline">مدیریت ←</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr><th class="px-4 py-3">دوره</th><th class="px-4 py-3 text-left">پایه</th><th class="px-4 py-3 text-left">پاداش</th><th class="px-4 py-3 text-left">کسورات</th><th class="px-4 py-3 text-left">خالص</th><th class="px-4 py-3">وضعیت</th></tr>
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
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-stone-400">هنوز لیست حقوقی اجرا نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Advances (read-only) --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-3">
            <span class="font-semibold text-stone-800">مساعده‌ها / قرض‌ها</span>
            <a href="{{ route('payroll.index') }}" wire:navigate class="text-sm text-amber-700 hover:underline">مدیریت ←</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr><th class="px-4 py-3">تاریخ</th><th class="px-4 py-3">یادداشت</th><th class="px-4 py-3 text-left">مبلغ</th><th class="px-4 py-3 text-left">بازیافت‌شده</th><th class="px-4 py-3 text-left">باقی‌مانده</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($employee->advances as $advance)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-3 text-stone-500">{{ $advance->advanced_on->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-stone-700">{{ $advance->note ?: '—' }}</td>
                            <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->amount) }}</td>
                            <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->recovered) }}</td>
                            <td class="px-4 py-3 text-left font-medium {{ $advance->outstanding() > 0 ? 'text-amber-700' : 'text-green-600' }}">{{ Format::money($advance->outstanding()) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-stone-400">هیچ مساعده‌ای پرداخت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
