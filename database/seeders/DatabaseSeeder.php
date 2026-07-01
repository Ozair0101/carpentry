<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Material;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectTask;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin login
        User::updateOrCreate(
            ['email' => 'admin@carpentry.test'],
            ['name' => 'Workshop Owner', 'password' => Hash::make('password')],
        );

        // Company settings
        Setting::updateOrCreate(['id' => 1], [
            'company_name' => 'Timberline Carpentry',
            'address' => "12 Sawmill Road\nWoodville",
            'phone' => '(555) 012-3456',
            'email' => 'hello@timberline.test',
            'currency' => 'USD',
            'tax_rate' => 8.00,
            'default_terms' => 'Payment due within 14 days. 30% deposit required to schedule work.',
        ]);

        // Materials price list
        $materials = collect([
            ['name' => 'Pine board (per m)', 'unit' => 'm', 'unit_price' => 6.50, 'category' => 'Timber'],
            ['name' => 'Oak board (per m)', 'unit' => 'm', 'unit_price' => 18.00, 'category' => 'Timber'],
            ['name' => 'Plywood sheet 18mm', 'unit' => 'sheet', 'unit_price' => 42.00, 'category' => 'Sheet goods'],
            ['name' => 'Wood screws (box)', 'unit' => 'box', 'unit_price' => 9.00, 'category' => 'Fixings'],
            ['name' => 'Cabinet hinge', 'unit' => 'unit', 'unit_price' => 3.20, 'category' => 'Hardware'],
            ['name' => 'Carpentry labour', 'unit' => 'hr', 'unit_price' => 55.00, 'category' => 'Labour'],
            ['name' => 'Wood finish / varnish (L)', 'unit' => 'L', 'unit_price' => 24.00, 'category' => 'Finishing'],
        ])->map(fn ($m) => Material::create($m));

        // Money accounts (always seeded so finance works out of the box)
        Account::updateOrCreate(['name' => 'Cash on hand'], ['type' => 'cash', 'opening_balance' => 2000, 'is_default' => true]);
        Account::updateOrCreate(['name' => 'Business bank'], ['type' => 'bank', 'opening_balance' => 15000]);

        // Income / expense categories
        collect([
            ['name' => 'Job income', 'kind' => 'income'],
            ['name' => 'Other income', 'kind' => 'income'],
            ['name' => 'Materials', 'kind' => 'expense'],
            ['name' => 'Wages & salaries', 'kind' => 'expense'],
            ['name' => 'Rent', 'kind' => 'expense'],
            ['name' => 'Utilities', 'kind' => 'expense'],
            ['name' => 'Tools & equipment', 'kind' => 'expense'],
            ['name' => 'Transport', 'kind' => 'expense'],
        ])->each(fn ($c) => TransactionCategory::firstOrCreate(['name' => $c['name']], $c));

        if (! app()->environment('production')) {
            $this->seedDemo($materials);
        }
    }

    protected function seedDemo($materials): void
    {
        $labour = $materials->firstWhere('name', 'Carpentry labour');
        $oak = $materials->firstWhere('name', 'Oak board (per m)');

        $customer = Customer::create([
            'name' => 'Sarah Whitfield',
            'company' => 'Whitfield Interiors',
            'email' => 'sarah@whitfield.test',
            'phone' => '(555) 987-6543',
            'billing_address' => "88 Maple Avenue\nWoodville",
            'notes' => 'Prefers oak. Repeat client.',
        ]);

        Customer::create([
            'name' => 'Tom Berger',
            'phone' => '(555) 222-1111',
            'billing_address' => '5 Elm Street',
        ]);

        // Estimate (approved)
        $estimate = Estimate::create([
            'customer_id' => $customer->id,
            'number' => 'EST-0001',
            'status' => 'approved',
            'issue_date' => now()->subDays(20),
            'valid_until' => now()->addDays(10),
            'tax_rate' => 8.00,
            'notes' => 'Fitted oak bookshelves for study.',
        ]);
        EstimateItem::create(['estimate_id' => $estimate->id, 'material_id' => $oak->id, 'description' => 'Oak board', 'qty' => 24, 'unit' => 'm', 'unit_price' => 18.00, 'line_total' => 432.00, 'position' => 1]);
        EstimateItem::create(['estimate_id' => $estimate->id, 'material_id' => $labour->id, 'description' => 'Carpentry labour', 'qty' => 16, 'unit' => 'hr', 'unit_price' => 55.00, 'line_total' => 880.00, 'position' => 2]);
        $estimate->load('items');
        $estimate->recalculate();

        // Job from the estimate
        $project = Project::create([
            'customer_id' => $customer->id,
            'estimate_id' => $estimate->id,
            'title' => 'Fitted oak bookshelves',
            'description' => 'Floor-to-ceiling bookshelves in the study.',
            'site_address' => "88 Maple Avenue\nWoodville",
            'status' => 'in_progress',
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
            'assigned_to' => 'James (lead carpenter)',
            'budget' => (float) $estimate->total,
        ]);
        ProjectTask::create(['project_id' => $project->id, 'title' => 'Site measure', 'is_done' => true, 'position' => 1]);
        ProjectTask::create(['project_id' => $project->id, 'title' => 'Cut & assemble carcass', 'is_done' => true, 'position' => 2]);
        ProjectTask::create(['project_id' => $project->id, 'title' => 'Install & finish on site', 'is_done' => false, 'position' => 3]);
        ProjectExpense::create(['project_id' => $project->id, 'type' => 'material', 'description' => 'Oak boards', 'qty' => 24, 'unit_cost' => 12.00, 'total' => 288.00, 'incurred_on' => now()->subDays(2)]);
        ProjectExpense::create(['project_id' => $project->id, 'type' => 'labour', 'description' => 'Workshop hours', 'qty' => 10, 'unit_cost' => 35.00, 'total' => 350.00, 'incurred_on' => now()->subDay()]);

        // Deposit invoice
        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'project_id' => $project->id,
            'estimate_id' => $estimate->id,
            'number' => 'INV-0001',
            'status' => 'sent',
            'issue_date' => now()->subDays(18),
            'due_date' => now()->subDays(4),
            'tax_rate' => 8.00,
        ]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'Deposit — bookshelf project', 'qty' => 1, 'unit_price' => 400.00, 'line_total' => 400.00, 'position' => 1]);
        $invoice->load('items');
        $invoice->recalculate();
        Payment::create(['invoice_id' => $invoice->id, 'amount' => 200.00, 'paid_on' => now()->subDays(15), 'method' => 'bank']);
        $invoice->syncPaymentStatus();

        // --- Finance demo: a supplier with an outstanding bill ---
        $supplier = Supplier::create([
            'name' => 'Woodville Timber Co.',
            'company' => 'Woodville Timber Co.',
            'phone' => '(555) 444-2200',
            'email' => 'sales@woodvilletimber.test',
        ]);
        $bill = Purchase::create([
            'supplier_id' => $supplier->id,
            'project_id' => $project->id,
            'reference' => 'WT-3391',
            'status' => 'unpaid',
            'bill_date' => now()->subDays(6),
            'due_date' => now()->addDays(8),
            'tax_total' => 24.00,
        ]);
        PurchaseItem::create(['purchase_id' => $bill->id, 'description' => 'Oak boards (bulk)', 'qty' => 24, 'unit' => 'm', 'unit_price' => 12.50, 'line_total' => 300.00, 'position' => 1]);
        $bill->load('items');
        $bill->recalculate();

        // --- Finance demo: employees, payroll and an advance ---
        $james = Employee::create(['name' => 'James Cole', 'role' => 'Lead carpenter', 'phone' => '(555) 100-2000', 'salary_type' => 'monthly', 'salary_rate' => 3200, 'joined_on' => now()->subYears(2), 'is_active' => true]);
        Employee::create(['name' => 'Mia Torres', 'role' => 'Finisher', 'salary_type' => 'daily', 'salary_rate' => 140, 'joined_on' => now()->subMonths(8), 'is_active' => true]);

        $james->advances()->create(['amount' => 500, 'recovered' => 0, 'advanced_on' => now()->subDays(10), 'note' => 'Personal advance']);
        $james->payrolls()->create([
            'period_label' => now()->subMonth()->format('F Y'),
            'base_amount' => 3200, 'bonus' => 0, 'deductions' => 0, 'advance_deducted' => 0,
            'net_amount' => 3200, 'status' => 'pending',
        ]);
    }
}
