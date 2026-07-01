<?php

use App\Http\Controllers\EstimatePdfController;
use App\Http\Controllers\InvoicePdfController;
use App\Livewire\Auth\Login;
use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\Estimates;
use App\Livewire\Invoices;
use App\Livewire\Materials;
use App\Livewire\Projects;
use App\Livewire\Schedule;
use App\Livewire\Settings;
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

    // Settings
    Route::get('/settings', Settings\Company::class)->name('settings.company');
});
