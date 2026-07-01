<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Support\DocumentNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageLoadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::current();
        $this->actingAs(User::factory()->create());
    }

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_all_index_pages_load(): void
    {
        foreach (['/dashboard', '/customers', '/materials', '/estimates', '/jobs', '/schedule', '/invoices', '/settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_create_pages_load(): void
    {
        foreach (['/customers/create', '/estimates/create', '/jobs/create', '/invoices/create'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_detail_and_pdf_pages_load(): void
    {
        $customer = Customer::create(['name' => 'Acme']);

        $estimate = Estimate::create(['customer_id' => $customer->id, 'number' => DocumentNumber::nextEstimate(), 'status' => 'draft', 'issue_date' => now(), 'tax_rate' => 5]);
        $estimate->items()->create(['description' => 'Work', 'qty' => 1, 'unit_price' => 100, 'line_total' => 100, 'position' => 1]);
        $estimate->load('items');
        $estimate->recalculate();

        $project = Project::create(['customer_id' => $customer->id, 'title' => 'Job', 'status' => 'scheduled']);

        $invoice = Invoice::create(['customer_id' => $customer->id, 'number' => DocumentNumber::nextInvoice(), 'status' => 'draft', 'issue_date' => now(), 'tax_rate' => 5]);
        $invoice->items()->create(['description' => 'Work', 'qty' => 1, 'unit_price' => 100, 'line_total' => 100, 'position' => 1]);
        $invoice->load('items');
        $invoice->recalculate();

        $this->get("/customers/{$customer->id}")->assertOk();
        $this->get("/estimates/{$estimate->id}")->assertOk();
        $this->get("/estimates/{$estimate->id}/pdf")->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get("/jobs/{$project->id}")->assertOk();
        $this->get("/invoices/{$invoice->id}")->assertOk();
        $this->get("/invoices/{$invoice->id}/pdf")->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
