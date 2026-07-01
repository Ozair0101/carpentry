<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Livewire\Component;

class Form extends Component
{
    public ?Employee $employee = null;

    public string $name = '';
    public string $role = '';
    public string $phone = '';
    public string $email = '';
    public string $salary_type = 'monthly';
    public $salary_rate = 0;
    public ?string $joined_on = null;
    public bool $is_active = true;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'salary_type' => 'required|in:'.implode(',', Employee::SALARY_TYPES),
            'salary_rate' => 'required|numeric|min:0',
            'joined_on' => 'nullable|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(?Employee $employee = null): void
    {
        if ($employee && $employee->exists) {
            $this->employee = $employee;
            $this->name = $employee->name;
            $this->role = (string) $employee->role;
            $this->phone = (string) $employee->phone;
            $this->email = (string) $employee->email;
            $this->salary_type = $employee->salary_type;
            $this->salary_rate = $employee->salary_rate;
            $this->joined_on = $employee->joined_on?->format('Y-m-d');
            $this->is_active = $employee->is_active;
            $this->notes = (string) $employee->notes;
        }
    }

    public function save()
    {
        $data = $this->validate();
        $data['joined_on'] = $data['joined_on'] ?: null;

        if ($this->employee) {
            $this->employee->update($data);
            $employee = $this->employee;
        } else {
            $employee = Employee::create($data);
        }

        session()->flash('status', $this->employee ? 'کارمند به‌روزرسانی شد.' : 'کارمند افزوده شد.');

        return $this->redirectRoute('employees.show', $employee, navigate: true);
    }

    public function render()
    {
        return view('livewire.employees.form')
            ->title($this->employee ? 'ویرایش کارمند' : 'کارمند جدید');
    }
}
