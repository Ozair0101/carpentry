<?php

namespace App\Livewire\Payroll;

use App\Models\Account;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Support\Ledger;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Central payroll desk: run and pay salaries and hand out / track advances for
 * every employee from one screen (previously only available per-employee).
 */
class Index extends Component
{
    use WithPagination;

    // --- Payroll run modal ---
    public bool $showPayrollForm = false;
    public $prEmployeeId = null;
    public string $periodLabel = '';
    public $payrollBase = 0;
    public $payrollBonus = 0;
    public $payrollOvertime = 0;
    public $payrollDeductions = 0;
    public $payrollAdvance = 0;

    // --- Pay-salary modal ---
    public ?int $payingPayrollId = null;
    public $payAmount = 0;
    public $payAccountId = null;
    public string $payDate = '';
    public string $payNote = '';

    // --- Advance modal ---
    public bool $showAdvanceForm = false;
    public $advEmployeeId = null;
    public $advAmount = 0;
    public $advAccountId = null;
    public string $advDate = '';
    public string $advNote = '';

    /* ------------------------------- Payroll ------------------------------ */
    public function openPayroll(?int $employeeId = null): void
    {
        $this->reset(['payrollBonus', 'payrollOvertime', 'payrollDeductions', 'payrollAdvance']);
        $this->prEmployeeId = $employeeId;
        $this->periodLabel = Carbon::now()->translatedFormat('F Y');
        $this->syncPayrollDefaults();
        $this->showPayrollForm = true;
    }

    /** Live net preview shown in the run modal. */
    public function previewNet(): float
    {
        return round(
            (float) $this->payrollBase + (float) $this->payrollBonus + (float) $this->payrollOvertime
            - (float) $this->payrollDeductions - (float) $this->payrollAdvance,
            2
        );
    }

    /** When the employee changes, prefill base pay and suggested advance recovery. */
    public function updatedPrEmployeeId(): void
    {
        $this->syncPayrollDefaults();
    }

    protected function syncPayrollDefaults(): void
    {
        $employee = $this->prEmployeeId ? Employee::find($this->prEmployeeId) : null;
        $this->payrollBase = $employee ? (float) $employee->salary_rate : 0;
        $this->payrollAdvance = $employee ? min($employee->advanceBalance(), (float) $employee->salary_rate) : 0;
    }

    public function savePayroll(): void
    {
        $this->validate([
            'prEmployeeId' => 'required|exists:employees,id',
            'periodLabel' => 'required|string|max:255',
            'payrollBase' => 'required|numeric|min:0',
            'payrollBonus' => 'required|numeric|min:0',
            'payrollDeductions' => 'required|numeric|min:0',
            'payrollAdvance' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($this->prEmployeeId);
        $payroll = $employee->payrolls()->create([
            'period_label' => $this->periodLabel,
            'base_amount' => (float) $this->payrollBase,
            'bonus' => (float) $this->payrollBonus,
            'overtime' => (float) $this->payrollOvertime,
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
        $payroll = Payroll::findOrFail($payrollId);
        $this->payingPayrollId = $payrollId;
        $this->payAmount = $payroll->balance();
        $this->payAccountId = Account::default()?->id;
        $this->payDate = Carbon::today()->format('Y-m-d');
        $this->payNote = '';
    }

    public function payPayroll(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:0.01',
            'payAccountId' => 'required|exists:accounts,id',
            'payDate' => 'required|date',
            'payNote' => 'nullable|string|max:255',
        ]);

        $payroll = Payroll::with('employee')->findOrFail($this->payingPayrollId);
        $wasPaid = $payroll->status === 'paid';

        Ledger::record(
            direction: 'out',
            amount: (float) $this->payAmount,
            accountId: (int) $this->payAccountId,
            source: $payroll,
            attributes: [
                'occurred_on' => $this->payDate,
                'description' => 'Salary '.$payroll->period_label.' — '.$payroll->employee->name,
                'reference' => $this->payNote ?: null,
            ],
        );

        $payroll->paid_on = $this->payDate;
        $payroll->save();
        $payroll->syncPaymentStatus();

        // Recover deducted advances once the salary is fully settled.
        if (! $wasPaid && $payroll->status === 'paid') {
            $payroll->employee->recoverAdvances((float) $payroll->advance_deducted);
        }

        $this->payingPayrollId = null;

        $balance = $payroll->balance();
        session()->flash('status', $balance > 0.001
            ? 'پرداخت جزئی ثبت شد. باقی‌مانده بدهی به کارمند: '.\App\Support\Format::money($balance)
            : 'معاش به‌طور کامل پرداخت شد.');
    }

    public function deletePayroll(int $id): void
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->payments()->delete();
        $payroll->delete();
        session()->flash('status', 'اجرای لیست حقوق حذف شد.');
    }

    /* ------------------------------ Advances ------------------------------ */
    public function openAdvance(?int $employeeId = null): void
    {
        $this->reset(['advAmount', 'advNote']);
        $this->advEmployeeId = $employeeId;
        $this->advAmount = 0;
        $this->advAccountId = Account::default()?->id;
        $this->advDate = Carbon::today()->format('Y-m-d');
        $this->showAdvanceForm = true;
    }

    public function saveAdvance(): void
    {
        $this->validate([
            'advEmployeeId' => 'required|exists:employees,id',
            'advAmount' => 'required|numeric|min:0.01',
            'advAccountId' => 'required|exists:accounts,id',
            'advDate' => 'required|date',
            'advNote' => 'nullable|string|max:255',
        ]);

        $employee = Employee::findOrFail($this->advEmployeeId);
        $advance = $employee->advances()->create([
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
                'description' => 'Advance to '.$employee->name,
            ],
        );

        $this->showAdvanceForm = false;
        session()->flash('status', 'مساعده ثبت شد.');
    }

    public function deleteAdvance(int $id): void
    {
        $advance = EmployeeAdvance::findOrFail($id);
        $advance->morphMany(Transaction::class, 'sourceable')->delete();
        $advance->delete();
        session()->flash('status', 'مساعده حذف شد.');
    }

    public function render()
    {
        $advances = EmployeeAdvance::with('employee')->latest('advanced_on')->latest('id')->get();

        return view('livewire.payroll.index', [
            'employees' => Employee::orderBy('name')->get(),
            'accounts' => Account::where('is_active', true)->orderBy('name')->get(),
            'payrolls' => Payroll::with(['employee', 'payments'])->latest('period_end')->latest('id')->paginate(15),
            'advances' => $advances,
            'pendingTotal' => Payroll::whereIn('status', ['pending', 'partial'])->get()->sum(fn (Payroll $p) => max(0, $p->balance())),
            'advancesOutstanding' => $advances->sum(fn (EmployeeAdvance $a) => max(0, $a->outstanding())),
        ])->title('لیست حقوق');
    }
}
