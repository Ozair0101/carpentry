<?php

namespace App\Livewire\Employees;

use App\Models\Account;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Payroll;
use App\Support\Ledger;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Show extends Component
{
    public Employee $employee;

    // Payroll run form
    public bool $showPayrollForm = false;
    public string $periodLabel = '';
    public $payrollBase = 0;
    public $payrollBonus = 0;
    public $payrollDeductions = 0;
    public $payrollAdvance = 0;

    // Pay payroll modal
    public ?int $payingPayrollId = null;
    public $payAccountId = null;
    public string $payDate = '';

    // Advance form
    public bool $showAdvanceForm = false;
    public $advAmount = 0;
    public $advAccountId = null;
    public string $advDate = '';
    public string $advNote = '';

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    // --- Payroll ---
    public function openPayroll(): void
    {
        $this->reset(['payrollBonus', 'payrollDeductions', 'payrollAdvance']);
        $this->periodLabel = Carbon::now()->format('F Y');
        $this->payrollBase = $this->employee->salary_rate;
        $this->payrollBonus = 0;
        $this->payrollDeductions = 0;
        // Suggest recovering outstanding advances, capped at base+bonus.
        $this->payrollAdvance = min($this->employee->advanceBalance(), (float) $this->employee->salary_rate);
        $this->showPayrollForm = true;
    }

    public function savePayroll(): void
    {
        $this->validate([
            'periodLabel' => 'required|string|max:255',
            'payrollBase' => 'required|numeric|min:0',
            'payrollBonus' => 'required|numeric|min:0',
            'payrollDeductions' => 'required|numeric|min:0',
            'payrollAdvance' => 'required|numeric|min:0',
        ]);

        $payroll = $this->employee->payrolls()->create([
            'period_label' => $this->periodLabel,
            'base_amount' => (float) $this->payrollBase,
            'bonus' => (float) $this->payrollBonus,
            'deductions' => (float) $this->payrollDeductions,
            'advance_deducted' => (float) $this->payrollAdvance,
            'status' => 'pending',
        ]);
        $payroll->update(['net_amount' => $payroll->computeNet()]);

        $this->showPayrollForm = false;
        session()->flash('status', 'اجرای لیست حقوق ایجاد شد.');
    }

    public function openPay(int $payrollId): void
    {
        $this->payingPayrollId = $payrollId;
        $this->payAccountId = Account::default()?->id;
        $this->payDate = Carbon::today()->format('Y-m-d');
    }

    public function payPayroll(): void
    {
        $this->validate([
            'payAccountId' => 'required|exists:accounts,id',
            'payDate' => 'required|date',
        ]);

        $payroll = $this->employee->payrolls()->findOrFail($this->payingPayrollId);

        if ($payroll->status === 'paid') {
            $this->payingPayrollId = null;

            return;
        }

        Ledger::record(
            direction: 'out',
            amount: (float) $payroll->net_amount,
            accountId: (int) $this->payAccountId,
            source: $payroll,
            attributes: [
                'occurred_on' => $this->payDate,
                'description' => 'Salary '.$payroll->period_label.' — '.$this->employee->name,
            ],
        );

        $payroll->update(['status' => 'paid', 'paid_on' => $this->payDate]);

        // Recover deducted advances against outstanding balances (oldest first).
        $this->recoverAdvances((float) $payroll->advance_deducted);

        $this->payingPayrollId = null;
        session()->flash('status', 'معاش پرداخت شد.');
    }

    protected function recoverAdvances(float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        foreach ($this->employee->advances()->orderBy('advanced_on')->get() as $advance) {
            if ($amount <= 0) {
                break;
            }
            $take = min($advance->outstanding(), $amount);
            if ($take > 0) {
                $advance->increment('recovered', $take);
                $amount -= $take;
            }
        }
    }

    public function deletePayroll(int $id): void
    {
        $payroll = $this->employee->payrolls()->findOrFail($id);
        $payroll->payments()->delete();
        $payroll->delete();
        session()->flash('status', 'اجرای لیست حقوق حذف شد.');
    }

    // --- Advances ---
    public function openAdvance(): void
    {
        $this->reset(['advAmount', 'advNote']);
        $this->advAmount = 0;
        $this->advAccountId = Account::default()?->id;
        $this->advDate = Carbon::today()->format('Y-m-d');
        $this->showAdvanceForm = true;
    }

    public function saveAdvance(): void
    {
        $this->validate([
            'advAmount' => 'required|numeric|min:0.01',
            'advAccountId' => 'required|exists:accounts,id',
            'advDate' => 'required|date',
            'advNote' => 'nullable|string|max:255',
        ]);

        $advance = $this->employee->advances()->create([
            'amount' => (float) $this->advAmount,
            'advanced_on' => $this->advDate,
            'note' => $this->advNote ?: null,
        ]);

        Ledger::record(
            direction: 'out',
            amount: (float) $this->advAmount,
            accountId: (int) $this->advAccountId,
            source: $advance,
            attributes: [
                'occurred_on' => $this->advDate,
                'description' => 'Advance to '.$this->employee->name,
            ],
        );

        $this->showAdvanceForm = false;
        session()->flash('status', 'مساعده ثبت شد.');
    }

    public function deleteAdvance(int $id): void
    {
        $advance = $this->employee->advances()->findOrFail($id);
        $advance->morphMany(\App\Models\Transaction::class, 'sourceable')->delete();
        $advance->delete();
        session()->flash('status', 'مساعده حذف شد.');
    }

    public function render()
    {
        $this->employee->load(['payrolls', 'advances']);

        return view('livewire.employees.show', [
            'accounts' => Account::where('is_active', true)->orderBy('name')->get(),
        ])->title($this->employee->name);
    }
}
