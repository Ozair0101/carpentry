<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    public ?Supplier $supplier = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public string $company = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('nullable|string')]
    public string $address = '';

    #[Validate('nullable|string')]
    public string $notes = '';

    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier && $supplier->exists) {
            $this->supplier = $supplier;
            $this->fill($supplier->only(['name', 'company', 'email', 'phone', 'address', 'notes']));
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->supplier) {
            $this->supplier->update($data);
            $supplier = $this->supplier;
        } else {
            $supplier = Supplier::create($data);
        }

        session()->flash('status', $this->supplier ? 'Supplier updated.' : 'Supplier created.');

        return $this->redirectRoute('suppliers.show', $supplier, navigate: true);
    }

    public function render()
    {
        return view('livewire.suppliers.form')
            ->title($this->supplier ? 'Edit supplier' : 'New supplier');
    }
}
