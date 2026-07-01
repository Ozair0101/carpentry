<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;

class Show extends Component
{
    public Supplier $supplier;

    public function mount(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    public function render()
    {
        $this->supplier->load(['purchases']);

        return view('livewire.suppliers.show')
            ->title($this->supplier->name);
    }
}
