<?php

namespace App\Livewire\Projects;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Setting;
use App\Support\DocumentNumber;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Show extends Component
{
    public Project $project;

    // Task quick-add
    public string $newTask = '';

    // Expense quick-add
    public string $expType = 'material';

    public string $expDescription = '';

    public $expQty = 1;

    public $expUnitCost = 0;

    // Appointment quick-add
    public string $apptTitle = '';

    public ?string $apptStart = null;

    public ?string $apptEnd = null;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    public function setStatus(string $status): void
    {
        abort_unless(in_array($status, Project::STATUSES, true), 400);
        $this->project->update(['status' => $status]);
    }

    // --- Tasks ---
    public function addTask(): void
    {
        $this->validate(['newTask' => 'required|string|max:255']);
        $this->project->tasks()->create([
            'title' => $this->newTask,
            'position' => ($this->project->tasks()->max('position') ?? 0) + 1,
        ]);
        $this->newTask = '';
    }

    public function toggleTask(int $id): void
    {
        $task = $this->project->tasks()->findOrFail($id);
        $task->update(['is_done' => ! $task->is_done]);
    }

    public function deleteTask(int $id): void
    {
        $this->project->tasks()->findOrFail($id)->delete();
    }

    // --- Expenses ---
    public function addExpense(): void
    {
        $this->validate([
            'expType' => 'required|in:'.implode(',', ProjectExpense::TYPES),
            'expDescription' => 'required|string|max:255',
            'expQty' => 'required|numeric|min:0',
            'expUnitCost' => 'required|numeric|min:0',
        ]);

        $this->project->expenses()->create([
            'type' => $this->expType,
            'description' => $this->expDescription,
            'qty' => (float) $this->expQty,
            'unit_cost' => (float) $this->expUnitCost,
            'total' => round((float) $this->expQty * (float) $this->expUnitCost, 2),
            'incurred_on' => Carbon::today(),
        ]);

        $this->reset(['expDescription', 'expQty', 'expUnitCost']);
        $this->expType = 'material';
        $this->expQty = 1;
    }

    public function deleteExpense(int $id): void
    {
        $this->project->expenses()->findOrFail($id)->delete();
    }

    // --- Appointments ---
    public function addAppointment(): void
    {
        $this->validate([
            'apptTitle' => 'required|string|max:255',
            'apptStart' => 'required|date',
            'apptEnd' => 'required|date|after:apptStart',
        ]);

        $this->project->appointments()->create([
            'title' => $this->apptTitle,
            'starts_at' => $this->apptStart,
            'ends_at' => $this->apptEnd,
        ]);

        $this->reset(['apptTitle', 'apptStart', 'apptEnd']);
    }

    public function deleteAppointment(int $id): void
    {
        $this->project->appointments()->findOrFail($id)->delete();
    }

    /**
     * Create a draft invoice from this job, copying the estimate's items when present.
     */
    public function createInvoice()
    {
        $settings = Setting::current();

        $invoice = Invoice::create([
            'customer_id' => $this->project->customer_id,
            'project_id' => $this->project->id,
            'estimate_id' => $this->project->estimate_id,
            'number' => DocumentNumber::nextInvoice(),
            'status' => 'draft',
            'issue_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(14),
            'tax_rate' => $this->project->estimate?->tax_rate ?? $settings->tax_rate,
            'terms' => $settings->default_terms,
        ]);

        if ($this->project->estimate) {
            foreach ($this->project->estimate->items as $item) {
                $invoice->items()->create($item->only([
                    'material_id', 'description', 'qty', 'unit', 'unit_price', 'line_total', 'position',
                ]));
            }
        } else {
            $invoice->items()->create([
                'description' => $this->project->title,
                'qty' => 1,
                'unit_price' => $this->project->budget ?? 0,
                'line_total' => $this->project->budget ?? 0,
                'position' => 1,
            ]);
        }

        $invoice->load('items');
        $invoice->recalculate();

        session()->flash('status', 'فاکتور '.$invoice->number.' از روی کار ایجاد شد.');

        return $this->redirectRoute('invoices.edit', $invoice, navigate: true);
    }

    public function delete()
    {
        $this->project->delete();
        session()->flash('status', 'کار حذف شد.');

        return $this->redirectRoute('jobs.index', navigate: true);
    }

    public function render()
    {
        $this->project->load(['customer', 'tasks', 'expenses', 'appointments', 'estimate', 'invoices']);

        $actual = (float) $this->project->expenses->sum('total');
        $budget = (float) $this->project->budget;
        $expensesByType = $this->project->expenses->groupBy('type')->map(fn ($g) => $g->sum('total'));

        return view('livewire.projects.show', [
            'actual' => $actual,
            'budget' => $budget,
            'margin' => $budget - $actual,
            'marginPct' => $budget > 0 ? round(($budget - $actual) / $budget * 100, 1) : null,
            'expensesByType' => $expensesByType,
        ])->title($this->project->title);
    }
}
