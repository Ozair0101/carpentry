<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $onlyActive = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employee::query()
            ->when($this->onlyActive, fn ($q) => $q->where('is_active', true))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('role', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.employees.index', ['employees' => $employees])
            ->title('Employees');
    }
}
