<?php use App\Support\Format; ?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">{{ $employee->name }}</h2>
            <p class="text-stone-500">{{ $employee->role }} · {{ Format::money($employee->salary_rate) }} / {{ $employee->salary_type }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openPayroll" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">Run payroll</button>
            <button wire:click="openAdvance" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Give advance</button>
            <a href="{{ route('employees.edit', $employee) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">Advance owed back</p>
            <p class="mt-1 text-xl font-bold {{ $employee->advanceBalance() > 0 ? 'text-amber-700' : 'text-stone-800' }}">{{ Format::money($employee->advanceBalance()) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">Unpaid salary</p>
            <p class="mt-1 text-xl font-bold {{ $employee->unpaidPayroll() > 0 ? 'text-red-600' : 'text-stone-800' }}">{{ Format::money($employee->unpaidPayroll()) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-stone-500">Joined</p>
            <p class="mt-1 text-xl font-bold text-stone-800">{{ $employee->joined_on?->format('M Y') ?: '—' }}</p>
        </div>
    </div>

    {{-- Payroll history --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-3 font-semibold text-stone-800">Payroll history</div>
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr><th class="px-4 py-3">Period</th><th class="px-4 py-3 text-right">Base</th><th class="px-4 py-3 text-right">Bonus</th><th class="px-4 py-3 text-right">Deductions</th><th class="px-4 py-3 text-right">Net</th><th class="px-4 py-3">Status</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->payrolls as $payroll)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $payroll->period_label }}</td>
                        <td class="px-4 py-3 text-right text-stone-600">{{ Format::money($payroll->base_amount) }}</td>
                        <td class="px-4 py-3 text-right text-stone-600">{{ Format::money($payroll->bonus) }}</td>
                        <td class="px-4 py-3 text-right text-stone-600">{{ Format::money((float) $payroll->deductions + (float) $payroll->advance_deducted) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ Format::money($payroll->net_amount) }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$payroll->status" /></td>
                        <td class="px-4 py-3 text-right">
                            @if ($payroll->status !== 'paid')
                                <button wire:click="openPay({{ $payroll->id }})" class="text-sm font-medium text-green-600 hover:text-green-800">Pay</button>
                            @endif
                            <button wire:click="deletePayroll({{ $payroll->id }})" wire:confirm="Remove this payroll run?" class="ml-2 text-stone-300 hover:text-red-600">✕</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-stone-400">No payroll runs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Advances --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-5 py-3 font-semibold text-stone-800">Advances / loans</div>
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Note</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3 text-right">Recovered</th><th class="px-4 py-3 text-right">Outstanding</th><th></th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->advances as $advance)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 text-stone-500">{{ $advance->advanced_on->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-stone-700">{{ $advance->note ?: '—' }}</td>
                        <td class="px-4 py-3 text-right text-stone-600">{{ Format::money($advance->amount) }}</td>
                        <td class="px-4 py-3 text-right text-stone-600">{{ Format::money($advance->recovered) }}</td>
                        <td class="px-4 py-3 text-right font-medium {{ $advance->outstanding() > 0 ? 'text-amber-700' : 'text-green-600' }}">{{ Format::money($advance->outstanding()) }}</td>
                        <td class="px-4 py-3 text-right"><button wire:click="deleteAdvance({{ $advance->id }})" wire:confirm="Remove this advance?" class="text-stone-300 hover:text-red-600">✕</button></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-400">No advances given.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payroll run modal --}}
    @if ($showPayrollForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="savePayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">Run payroll</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Period</label>
                    <input type="text" wire:model="periodLabel" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('periodLabel') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">Base</label><input type="number" step="0.01" wire:model="payrollBase" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">Bonus</label><input type="number" step="0.01" wire:model="payrollBonus" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">Deductions</label><input type="number" step="0.01" wire:model="payrollDeductions" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><label class="mb-1 block text-sm font-medium text-stone-700">Advance recovery</label><input type="number" step="0.01" wire:model="payrollAdvance" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></div>
                </div>
                <p class="text-xs text-stone-500">Advance owed: {{ Format::money($employee->advanceBalance()) }}</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showPayrollForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Create run</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Pay payroll modal --}}
    @if ($payingPayrollId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="payPayroll" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">Pay salary</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Pay from account</label>
                    <select wire:model="payAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— select —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('payAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Date</label>
                    <input type="date" wire:model="payDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('payingPayrollId', null)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Pay</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Advance modal --}}
    @if ($showAdvanceForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <form wire:submit="saveAdvance" class="w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">Give advance</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Amount</label>
                    <input type="number" step="0.01" wire:model="advAmount" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('advAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Pay from account</label>
                    <select wire:model="advAccountId" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— select —</option>
                        @foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                    </select>
                    @error('advAccountId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Date</label>
                    <input type="date" wire:model="advDate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Note</label>
                    <input type="text" wire:model="advNote" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showAdvanceForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Give advance</button>
                </div>
            </form>
        </div>
    @endif
</div>
