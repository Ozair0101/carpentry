<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Livewire\Component;

class Show extends Component
{
    public Employee $employee;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function render()
    {
        $this->employee->load(['payrolls', 'advances']);

        return view('livewire.employees.show')->title($this->employee->name);
    }
}
