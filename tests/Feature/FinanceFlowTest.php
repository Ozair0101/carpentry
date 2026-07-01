<?php

namespace Tests\Feature;

use App\Livewire\Employees\Show as EmployeeShow;
use App\Livewire\Invoices\Show as InvoiceShow;
use App\Livewire\Purchases\Show as PurchaseShow;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Support\DocumentNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::current();
        $this->actingAs(User::factory()->create());
        $this->account = Account::create(['name' => 'Cash', 'type' => 'cash', 'opening_balance' => 1000, 'is_default' => true]);
    }

    public function test_account_balance_reflects_income_and_expense(): void
    {
        Transaction::create(['account_id' => $this->account->id, 'direction' => 'in', 'amount' => 500, 'occurred_on' => now()]);
        Transaction::create(['account_id' => $this->account->id, 'direction' => 'out', 'amount' => 200, 'occurred_on' => now()]);

        $this->assertEquals(1300.0, $this->account->fresh()->balance());
    }

    public function test_invoice_payment_posts_income_to_the_ledger(): void
    {
        $customer = Customer::create(['name' => 'Client']);
        $invoice = Invoice::create(['customer_id' => $customer->id, 'number' => DocumentNumber::nextInvoice(), 'status' => 'sent', 'issue_date' => now(), 'tax_rate' => 0]);
        $invoice->items()->create(['description' => 'Work', 'qty' => 1, 'unit_price' => 300, 'line_total' => 300, 'position' => 1]);
        $invoice->load('items');
        $invoice->recalculate();

        Livewire::test(InvoiceShow::class, ['invoice' => $invoice])
            ->call('openPayment')
            ->set('payAmount', 300)
            ->set('payAccountId', $this->account->id)
            ->call('recordPayment');

        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals(1300.0, $this->account->fresh()->balance()); // opening 1000 + 300 in
        $this->assertDatabaseHas('transactions', ['direction' => 'in', 'amount' => 300.00, 'account_id' => $this->account->id]);
    }

    public function test_supplier_bill_payment_posts_expense_and_updates_status(): void
    {
        $supplier = Supplier::create(['name' => 'Timber Co']);
        $bill = Purchase::create(['supplier_id' => $supplier->id, 'status' => 'unpaid', 'bill_date' => now(), 'tax_total' => 0]);
        PurchaseItem::create(['purchase_id' => $bill->id, 'description' => 'Oak', 'qty' => 1, 'unit_price' => 400, 'line_total' => 400, 'position' => 1]);
        $bill->load('items');
        $bill->recalculate();

        // Partial payment
        Livewire::test(PurchaseShow::class, ['purchase' => $bill])
            ->call('openPayment')
            ->set('payAmount', 150)
            ->set('payAccountId', $this->account->id)
            ->call('recordPayment');

        $bill->refresh();
        $this->assertEquals('partial', $bill->status);
        $this->assertEquals(250.0, $bill->balance());
        $this->assertEquals(850.0, $this->account->fresh()->balance()); // 1000 − 150
        $this->assertEquals(250.0, $supplier->fresh()->balanceOwed());
    }

    public function test_paying_salary_posts_expense_and_recovers_advance(): void
    {
        $employee = Employee::create(['name' => 'James', 'salary_type' => 'monthly', 'salary_rate' => 1000, 'is_active' => true]);

        // Give a 200 advance (money out) then run payroll recovering it.
        Livewire::test(EmployeeShow::class, ['employee' => $employee])
            ->call('openAdvance')
            ->set('advAmount', 200)
            ->set('advAccountId', $this->account->id)
            ->call('saveAdvance');

        $this->assertEquals(200.0, $employee->fresh()->advanceBalance());
        $this->assertEquals(800.0, $this->account->fresh()->balance()); // 1000 − 200

        // Run payroll: base 1000, recover 200 advance → net 800
        Livewire::test(EmployeeShow::class, ['employee' => $employee])
            ->call('openPayroll')
            ->set('payrollBase', 1000)
            ->set('payrollBonus', 0)
            ->set('payrollDeductions', 0)
            ->set('payrollAdvance', 200)
            ->call('savePayroll');

        $payroll = $employee->fresh()->payrolls()->first();
        $this->assertEquals(800.0, (float) $payroll->net_amount);

        // Pay it
        Livewire::test(EmployeeShow::class, ['employee' => $employee])
            ->call('openPay', $payroll->id)
            ->set('payAccountId', $this->account->id)
            ->call('payPayroll');

        $this->assertEquals('paid', $payroll->fresh()->status);
        $this->assertEquals(0.0, $employee->fresh()->advanceBalance()); // advance recovered
        $this->assertEquals(0.0, $this->account->fresh()->balance()); // 800 − 800 net salary
    }

    public function test_phase2_pages_load(): void
    {
        $supplier = Supplier::create(['name' => 'Timber Co']);
        $bill = Purchase::create(['supplier_id' => $supplier->id, 'status' => 'unpaid', 'bill_date' => now(), 'tax_total' => 0]);
        $employee = Employee::create(['name' => 'James', 'salary_type' => 'monthly', 'salary_rate' => 1000]);

        foreach ([
            '/finance', '/finance/accounts', '/finance/transactions',
            '/suppliers', '/suppliers/create', "/suppliers/{$supplier->id}",
            '/bills', '/bills/create', "/bills/{$bill->id}",
            '/employees', '/employees/create', "/employees/{$employee->id}",
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_transfer_moves_money_between_accounts(): void
    {
        $bank = Account::create(['name' => 'Bank', 'type' => 'bank', 'opening_balance' => 0]);

        Livewire::test(\App\Livewire\Finance\Transactions::class)
            ->call('openForm', 'transfer')
            ->set('account_id', $this->account->id)
            ->set('to_account_id', $bank->id)
            ->set('amount', 400)
            ->call('save');

        $this->assertEquals(600.0, $this->account->fresh()->balance()); // 1000 − 400
        $this->assertEquals(400.0, $bank->fresh()->balance());
    }
}
