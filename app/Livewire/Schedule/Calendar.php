<?php

namespace App\Livewire\Schedule;

use App\Models\Appointment;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class Calendar extends Component
{
    #[Url]
    public string $month = '';

    // Quick-add form
    public bool $showForm = false;

    public string $title = '';

    public ?int $project_id = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public function mount(): void
    {
        if (! $this->month) {
            $this->month = Carbon::today()->format('Y-m');
        }
    }

    public function goToMonth(int $offset): void
    {
        $this->month = $this->cursor()->addMonths($offset)->format('Y-m');
    }

    public function today(): void
    {
        $this->month = Carbon::today()->format('Y-m');
    }

    protected function cursor(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->month.'-01')->startOfDay();
    }

    public function openForm(?string $date = null): void
    {
        $this->reset(['title', 'project_id', 'starts_at', 'ends_at']);
        $base = $date ? Carbon::parse($date) : Carbon::today();
        $this->starts_at = $base->copy()->setTime(9, 0)->format('Y-m-d\TH:i');
        $this->ends_at = $base->copy()->setTime(11, 0)->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        Appointment::create([
            'title' => $this->title,
            'project_id' => $this->project_id ?: null,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ]);

        $this->showForm = false;
        session()->flash('status', 'Appointment added.');
    }

    public function deleteAppointment(int $id): void
    {
        Appointment::findOrFail($id)->delete();
        session()->flash('status', 'Appointment removed.');
    }

    public function render()
    {
        $cursor = $this->cursor();
        $gridStart = $cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $cursor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $appointments = Appointment::with('project')
            ->whereBetween('starts_at', [$gridStart, $gridEnd->copy()->endOfDay()])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn ($a) => $a->starts_at->format('Y-m-d'));

        $days = collect();
        for ($d = $gridStart->copy(); $d->lte($gridEnd); $d->addDay()) {
            $days->push($d->copy());
        }

        return view('livewire.schedule.calendar', [
            'cursor' => $cursor,
            'days' => $days,
            'appointments' => $appointments,
            'projects' => Project::orderBy('title')->get(['id', 'title']),
        ])->title('Schedule');
    }
}
