<?php

namespace App\Livewire\Estimates;

use App\Models\Estimate;
use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Component;

class Show extends Component
{
    public Estimate $estimate;

    public function mount(Estimate $estimate): void
    {
        $this->estimate = $estimate;
    }

    public function setStatus(string $status): void
    {
        abort_unless(in_array($status, Estimate::STATUSES, true), 400);

        $this->estimate->update(['status' => $status]);
        session()->flash('status', 'برآورد علامت‌گذاری شد به عنوان '.$status.'.');
    }

    /**
     * Convert an approved estimate into a Job.
     */
    public function convertToJob()
    {
        if ($this->estimate->project()->exists()) {
            session()->flash('status', 'برای این برآورد قبلاً کار ایجاد شده است.');

            return $this->redirectRoute('jobs.show', $this->estimate->project, navigate: true);
        }

        if ($this->estimate->status !== 'approved') {
            $this->estimate->update(['status' => 'approved']);
        }

        $project = Project::create([
            'customer_id' => $this->estimate->customer_id,
            'estimate_id' => $this->estimate->id,
            'title' => $this->estimate->notes ? Str::limit($this->estimate->notes, 60) : 'کار از روی '.$this->estimate->number,
            'description' => $this->estimate->notes,
            'status' => 'scheduled',
            'budget' => $this->estimate->total,
        ]);

        session()->flash('status', 'کار از روی برآورد ایجاد شد.');

        return $this->redirectRoute('jobs.show', $project, navigate: true);
    }

    public function delete()
    {
        $this->estimate->delete();
        session()->flash('status', 'برآورد حذف شد.');

        return $this->redirectRoute('estimates.index', navigate: true);
    }

    public function render()
    {
        $this->estimate->load(['customer', 'items', 'project']);

        return view('livewire.estimates.show')
            ->title($this->estimate->number);
    }
}
