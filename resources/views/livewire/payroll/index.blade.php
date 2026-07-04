<?php use App\Support\Format; ?>
<div class="space-y-6" x-data="{ tab: 'payroll' }">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">لیست حقوق و مساعده‌ها</h2>
            <p class="text-sm text-stone-500">اجرا و پرداخت معاش کارمندان و مدیریت مساعده‌ها در یک جا</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openPayroll" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">+ اجرای لیست حقوق</button>
            <button wire:click="openAdvance" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">+ ثبت مساعده</button>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">معاشات پرداخت‌نشده</p>
            <p class="mt-1 text-2xl font-bold {{ $pendingTotal > 0 ? 'text-red-600' : 'text-stone-800' }}">{{ Format::money($pendingTotal) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">مساعده‌های قابل بازیافت</p>
            <p class="mt-1 text-2xl font-bold {{ $advancesOutstanding > 0 ? 'text-amber-700' : 'text-stone-800' }}">{{ Format::money($advancesOutstanding) }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-stone-200">
        <nav class="flex gap-6 text-sm font-medium">
            <button @click="tab = 'payroll'" :class="tab === 'payroll' ? 'border-amber-600 text-amber-700' : 'border-transparent text-stone-500 hover:text-stone-700'" class="border-b-2 px-1 pb-3">اجراهای لیست حقوق</button>
            <button @click="tab = 'advances'" :class="tab === 'advances' ? 'border-amber-600 text-amber-700' : 'border-transparent text-stone-500 hover:text-stone-700'" class="border-b-2 px-1 pb-3">مساعده‌ها</button>
        </nav>
    </div>

    {{-- Payroll runs --}}
    <div x-show="tab === 'payroll'" class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-4 py-3">کارمند</th>
                        <th class="px-4 py-3">دوره</th>
                        <th class="px-4 py-3 text-left">اضافه‌کاری</th>
                        <th class="px-4 py-3 text-left">خالص</th>
                        <th class="px-4 py-3 text-left">پرداخت‌شده</th>
                        <th class="px-4 py-3 text-left">باقی‌مانده</th>
                        <th class="px-4 py-3">وضعیت</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($payrolls as $payroll)
                        @php $bal = $payroll->balance(); @endphp
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-3 font-medium text-stone-800">{{ $payroll->employee->name }}</td>
                            <td class="px-4 py-3 text-stone-600">{{ $payroll->period_label }}</td>
                            <td class="px-4 py-3 text-left {{ $payroll->overtime > 0 ? 'font-medium text-blue-600' : 'text-stone-400' }}">{{ Format::money($payroll->overtime) }}</td>
                            <td class="px-4 py-3 text-left font-medium text-stone-800">{{ Format::money($payroll->net_amount) }}</td>
                            <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($payroll->amountPaid()) }}</td>
                            <td class="px-4 py-3 text-left {{ $bal > 0.001 ? 'font-medium text-amber-700' : ($bal < -0.001 ? 'font-medium text-blue-600' : 'text-stone-400') }}">
                                {{ Format::money($bal) }}
                                @if ($bal > 0.001)<span class="block text-[10px] font-normal text-stone-400">بدهی به کارمند</span>
                                @elseif ($bal < -0.001)<span class="block text-[10px] font-normal text-stone-400">اضافه‌پرداخت</span>@endif
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$payroll->status" /></td>
                            <td class="px-4 py-3 text-left">
                                @if ($payroll->status !== 'paid')
                                    <button wire:click="openPay({{ $payroll->id }})" class="text-sm font-medium text-green-600 hover:text-green-800">{{ $payroll->status === 'partial' ? 'پرداخت باقی‌مانده' : 'پرداخت' }}</button>
                                @endif
                                <button wire:click="deletePayroll({{ $payroll->id }})" wire:confirm="این اجرای لیست حقوق و پرداخت‌های آن حذف شود؟" class="mr-2 text-stone-300 hover:text-red-600">✕</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-stone-400">هنوز لیست حقوقی اجرا نشده است. با «اجرای لیست حقوق» شروع کنید.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payrolls->hasPages())
            <div class="border-t border-stone-100 p-3">{{ $payrolls->links() }}</div>
        @endif
    </div>

    {{-- Advances --}}
    <div x-show="tab === 'advances'" style="display:none" class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-4 py-3">کارمند</th>
                        <th class="px-4 py-3">تاریخ</th>
                        <th class="px-4 py-3">یادداشت</th>
                        <th class="px-4 py-3 text-left">مبلغ</th>
                        <th class="px-4 py-3 text-left">بازیافت‌شده</th>
                        <th class="px-4 py-3 text-left">باقی‌مانده</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($advances as $advance)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-3 font-medium text-stone-800">{{ $advance->employee->name }}</td>
                            <td class="px-4 py-3 text-stone-500">{{ $advance->advanced_on->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-stone-700">{{ $advance->note ?: '—' }}</td>
                            <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->amount) }}</td>
                            <td class="px-4 py-3 text-left text-stone-600">{{ Format::money($advance->recovered) }}</td>
                            <td class="px-4 py-3 text-left font-medium {{ $advance->outstanding() > 0 ? 'text-amber-700' : 'text-green-600' }}">{{ Format::money($advance->outstanding()) }}</td>
                            <td class="px-4 py-3 text-left"><button wire:click="deleteAdvance({{ $advance->id }})" wire:confirm="این مساعده حذف شود؟" class="text-stone-300 hover:text-red-600">✕</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-stone-400">هیچ مساعده‌ای ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payroll run modal --}}
    @if ($showPayrollForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="savePayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">اجرای لیست حقوق</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">کارمند</label>
                    <select wire:model.live="prEmployeeId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب کارمند —</option>
                        @foreach ($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                    </select>
                    @error('prEmployeeId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">دوره</label>
                    <input type="text" wire:model="periodLabel" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('periodLabel') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">پایه</label><input type="number" step="0.01" wire:model.live="payrollBase" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">پاداش</label><input type="number" step="0.01" wire:model.live="payrollBonus" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-blue-700">اضافه‌کاری</label><input type="number" step="0.01" wire:model.live="payrollOvertime" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">کسورات</label><input type="number" step="0.01" wire:model.live="payrollDeductions" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div class="col-span-2"><label class="mb-1 block text-sm font-medium text-stone-700">بازیافت مساعده</label><input type="number" step="0.01" wire:model.live="payrollAdvance" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2 text-sm">
                    <span class="text-stone-600">خالص قابل پرداخت</span>
                    <span class="text-base font-bold text-stone-800">{{ Format::money($this->previewNet()) }}</span>
                </div>

                {{-- Pay immediately: generate + pay in one step --}}
                <div class="space-y-3 rounded-lg border border-stone-200 p-3">
                    <label class="flex items-center gap-2 text-sm font-medium text-stone-700">
                        <input type="checkbox" wire:model.live="payNow" class="rounded border-stone-300 text-green-600 focus:ring-green-500">
                        پرداخت فوری معاش
                    </label>
                    @if ($payNow)
                        <div>
                            <label class="mb-1 block text-sm font-medium text-stone-700">پرداخت از حساب</label>
                            <select wire:model="payAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                                <option value="">— انتخاب کنید —</option>
                                @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                            </select>
                            @error('payAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ پرداخت</label>
                            <input type="date" wire:model="payDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                            @error('payDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <p class="text-xs text-stone-400">خالص معاش کامل از این حساب پرداخت می‌شود. برای پرداخت جزئی، این گزینه را بردارید و بعداً «پرداخت» را بزنید.</p>
                    @else
                        <p class="text-xs text-stone-400">فقط اجرای لیست حقوق ثبت می‌شود؛ پرداخت را بعداً از دکمهٔ «پرداخت» انجام دهید.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showPayrollForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <x-save-button class="!px-4" target="savePayroll" label="{{ $payNow ? 'ایجاد و پرداخت' : 'ایجاد اجرا' }}" busy="در حال ثبت…" />
                </div>
            </form>
        </div>
    @endif

    {{-- Pay salary modal --}}
    @if ($payingPayrollId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="payPayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">پرداخت معاش</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">مبلغ پرداخت</label>
                    <input type="number" step="0.01" wire:model="payAmount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('payAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-stone-400">اگر کمتر از باقی‌مانده بپردازید، بقیه به‌عنوان بدهی به کارمند ثبت می‌شود. برای پرداخت بیشتر بابت اضافه‌کاری، آن را در اجرای لیست حقوق وارد کنید.</p>
                </div>
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
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">یادداشت / دلیل (اختیاری)</label>
                    <input type="text" wire:model="payNote" placeholder="مثلاً پرداخت جزئی، اضافه‌کاری…" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('payingPayrollId', null)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <button type="submit" wire:target="payPayroll" wire:loading.attr="disabled" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:opacity-70">
                        <span wire:loading.remove wire:target="payPayroll">پرداخت</span>
                        <span wire:loading wire:target="payPayroll">در حال پرداخت…</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Advance modal --}}
    @if ($showAdvanceForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="saveAdvance" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">ثبت مساعده</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">کارمند</label>
                    <select wire:model="advEmployeeId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— انتخاب کارمند —</option>
                        @foreach ($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                    </select>
                    @error('advEmployeeId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
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
                    <x-save-button class="!px-4" target="saveAdvance" label="ثبت مساعده" busy="در حال ثبت…" />
                </div>
            </form>
        </div>
    @endif
</div>
