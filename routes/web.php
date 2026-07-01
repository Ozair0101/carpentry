<?php

use App\Http\Controllers\EstimatePdfController;
use App\Http\Controllers\InvoicePdfController;
use App\Livewire\Auth\Login;
use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\Employees;
use App\Livewire\Estimates;
use App\Livewire\Finance;
use App\Livewire\Invoices;
use App\Livewire\Materials;
use App\Livewire\Projects;
use App\Livewire\Purchases;
use App\Livewire\Reports;
use App\Livewire\Schedule;
use App\Livewire\Settings;
use App\Livewire\Suppliers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Customers
    Route::get('/customers', Customers\Index::class)->name('customers.index');
    Route::get('/customers/create', Customers\Form::class)->name('customers.create');
    Route::get('/customers/{customer}/edit', Customers\Form::class)->name('customers.edit');
    Route::get('/customers/{customer}', Customers\Show::class)->name('customers.show');

    // Materials price list
    Route::get('/materials', Materials\Index::class)->name('materials.index');

    // Estimates / Quotes
    Route::get('/estimates', Estimates\Index::class)->name('estimates.index');
    Route::get('/estimates/create', Estimates\Form::class)->name('estimates.create');
    Route::get('/estimates/{estimate}/edit', Estimates\Form::class)->name('estimates.edit');
    Route::get('/estimates/{estimate}', Estimates\Show::class)->name('estimates.show');
    Route::get('/estimates/{estimate}/pdf', EstimatePdfController::class)->name('estimates.pdf');

    // Jobs (stored as projects)
    Route::get('/jobs', Projects\Index::class)->name('jobs.index');
    Route::get('/jobs/create', Projects\Form::class)->name('jobs.create');
    Route::get('/jobs/{project}/edit', Projects\Form::class)->name('jobs.edit');
    Route::get('/jobs/{project}', Projects\Show::class)->name('jobs.show');

    // Schedule
    Route::get('/schedule', Schedule\Calendar::class)->name('schedule');

    // Invoices & payments
    Route::get('/invoices', Invoices\Index::class)->name('invoices.index');
    Route::get('/invoices/create', Invoices\Form::class)->name('invoices.create');
    Route::get('/invoices/{invoice}/edit', Invoices\Form::class)->name('invoices.edit');
    Route::get('/invoices/{invoice}', Invoices\Show::class)->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', InvoicePdfController::class)->name('invoices.pdf');

    // Finance
    Route::get('/finance', Finance\Overview::class)->name('finance');
    Route::get('/finance/accounts', Finance\Accounts::class)->name('accounts.index');
    Route::get('/finance/transactions', Finance\Transactions::class)->name('transactions.index');

    // Reports
    Route::get('/reports/income-statement', Reports\IncomeStatement::class)->name('reports.income');
    Route::get('/reports/balance-sheet', Reports\BalanceSheet::class)->name('reports.balance');
    Route::get('/reports/monthly', Reports\Monthly::class)->name('reports.monthly');

    // Suppliers
    Route::get('/suppliers', Suppliers\Index::class)->name('suppliers.index');
    Route::get('/suppliers/create', Suppliers\Form::class)->name('suppliers.create');
    Route::get('/suppliers/{supplier}/edit', Suppliers\Form::class)->name('suppliers.edit');
    Route::get('/suppliers/{supplier}', Suppliers\Show::class)->name('suppliers.show');

    // Bills / purchases (payables)
    Route::get('/bills', Purchases\Index::class)->name('bills.index');
    Route::get('/bills/create', Purchases\Form::class)->name('bills.create');
    Route::get('/bills/{purchase}/edit', Purchases\Form::class)->name('bills.edit');
    Route::get('/bills/{purchase}', Purchases\Show::class)->name('bills.show');

    // Employees & payroll
    Route::get('/employees', Employees\Index::class)->name('employees.index');
    Route::get('/employees/create', Employees\Form::class)->name('employees.create');
    Route::get('/employees/{employee}/edit', Employees\Form::class)->name('employees.edit');
    Route::get('/employees/{employee}', Employees\Show::class)->name('employees.show');

    // Settings
    Route::get('/settings', Settings\Company::class)->name('settings.company');
});
