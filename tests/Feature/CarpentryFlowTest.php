<?php

namespace Tests\Feature;

use App\Livewire\Estimates\Show as EstimateShow;
use App\Livewire\Invoices\Show as InvoiceShow;
use App\Livewire\Projects\Show as ProjectShow;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Setting;
use App\Models\User;
use App\Support\DocumentNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarpentryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::current(); // ensure defaults exist
        $this->actingAs(User::factory()->create());
    }

    private function makeEstimate(): Estimate
    {
        $customer = Customer::create(['name' => 'Test Client']);

        $estimate = Estimate::create([
            'customer_id' => $customer->id,
            'number' => DocumentNumber::nextEstimate(),
            'status' => 'draft',
            'issue_date' => now(),
            'tax_rate' => 10,
        ]);
        $estimate->items()->create(['description' => 'Oak', 'qty' => 2, 'unit_price' => 100, 'line_total' => 200, 'position' => 1]);
        $estimate->items()->create(['description' => 'Labour', 'qty' => 5, 'unit_price' => 50, 'line_total' => 250, 'position' => 2]);
        $estimate->load('items');
        $estimate->recalculate();

        return $estimate;
    }

    public function test_estimate_totals_are_calculated_with_tax(): void
    {
        $estimate = $this->makeEstimate();

        $this->assertEquals(450.00, $estimate->subtotal);
        $this->assertEquals(45.00, $estimate->tax_total);
        $this->assertEquals(495.00, $estimate->total);
    }

    public function test_document_numbers_increment_sequentially(): void
    {
        $this->assertSame('EST-0001', DocumentNumber::nextEstimate());
        $this->assertSame('EST-0002', DocumentNumber::nextEstimate());
        $this->assertSame('INV-0001', DocumentNumber::nextInvoice());
    }

    public function test_estimate_converts_to_job(): void
    {
        $estimate = $this->makeEstimate();

        Livewire::test(EstimateShow::class, ['estimate' => $estimate])
            ->call('convertToJob');

        $this->assertDatabaseHas('projects', [
            'estimate_id' => $estimate->id,
            'customer_id' => $estimate->customer_id,
            'budget' => 495.00,
        ]);
        $this->assertEquals('approved', $estimate->fresh()->status);
    }

    public function test_job_costing_tracks_actual_cost_and_margin(): void
    {
        $estimate = $this->makeEstimate();
        Livewire::test(EstimateShow::class, ['estimate' => $estimate])->call('convertToJob');
        $project = $estimate->fresh()->project;

        Livewire::test(ProjectShow::class, ['project' => $project])
            ->set('expType', 'material')
            ->set('expDescription', 'Timber')
            ->set('expQty', 3)
            ->set('expUnitCost', 40)
            ->call('addExpense');

        $this->assertEquals(120.00, $project->fresh()->actualCost());
    }

    public function test_full_pipeline_invoice_and_payment_marks_paid(): void
    {
        $estimate = $this->makeEstimate();
        Livewire::test(EstimateShow::class, ['estimate' => $estimate])->call('convertToJob');
        $project = $estimate->fresh()->project;

        // Create invoice from job (copies estimate items)
        Livewire::test(ProjectShow::class, ['project' => $project])->call('createInvoice');

        $invoice = $project->fresh()->invoices()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(495.00, $invoice->total);

        // Record full payment
        Livewire::test(InvoiceShow::class, ['invoice' => $invoice])
            ->call('openPayment')
            ->set('payAmount', 495.00)
            ->call('recordPayment');

        $invoice->refresh();
        $this->assertEquals(495.00, $invoice->amount_paid);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0.0, $invoice->balance());
    }

    public function test_partial_payment_sets_partial_status(): void
    {
        $estimate = $this->makeEstimate();
        Livewire::test(EstimateShow::class, ['estimate' => $estimate])->call('convertToJob');
        $project = $estimate->fresh()->project;
        Livewire::test(ProjectShow::class, ['project' => $project])->call('createInvoice');
        $invoice = $project->fresh()->invoices()->first();

        Livewire::test(InvoiceShow::class, ['invoice' => $invoice])
            ->call('openPayment')
            ->set('payAmount', 200.00)
            ->call('recordPayment');

        $this->assertEquals('partial', $invoice->fresh()->status);
    }
}
