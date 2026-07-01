<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function render()
    {
        $this->customer->load(['estimates', 'projects', 'invoices']);

        return view('livewire.customers.show')
            ->title($this->customer->name);
    }
}
