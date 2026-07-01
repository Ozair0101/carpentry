<?php

namespace App\Livewire\Projects;

use App\Models\Customer;
use App\Models\Project;
use Livewire\Component;

class Form extends Component
{
    public ?Project $project = null;

    public $customer_id = null;

    public string $title = '';

    public string $description = '';

    public string $site_address = '';

    public string $status = 'scheduled';

    public ?string $start_date = null;

    public ?string $due_date = null;

    public string $assigned_to = '';

    public $budget = null;

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'site_address' => 'nullable|string',
            'status' => 'required|in:'.implode(',', Project::STATUSES),
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
        ];
    }

    public function mount(?Project $project = null): void
    {
        if ($project && $project->exists) {
            $this->project = $project;
            $this->customer_id = $project->customer_id;
            $this->title = $project->title;
            $this->description = (string) $project->description;
            $this->site_address = (string) $project->site_address;
            $this->status = $project->status;
            $this->start_date = $project->start_date?->format('Y-m-d');
            $this->due_date = $project->due_date?->format('Y-m-d');
            $this->assigned_to = (string) $project->assigned_to;
            $this->budget = $project->budget;
        } else {
            $this->customer_id = request()->integer('customer') ?: null;
        }
    }

    public function save()
    {
        $data = $this->validate();
        $data['start_date'] = $data['start_date'] ?: null;
        $data['due_date'] = $data['due_date'] ?: null;

        if ($this->project) {
            $this->project->update($data);
            $project = $this->project;
        } else {
            $project = Project::create($data);
        }

        session()->flash('status', $this->project ? 'کار به‌روزرسانی شد.' : 'کار ایجاد شد.');

        return $this->redirectRoute('jobs.show', $project, navigate: true);
    }

    public function render()
    {
        return view('livewire.projects.form', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
        ])->title($this->project ? 'Edit job' : 'New job');
    }
}
