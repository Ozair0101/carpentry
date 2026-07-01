<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    public ?Customer $customer = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public string $company = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('nullable|string')]
    public string $billing_address = '';

    #[Validate('nullable|string')]
    public string $notes = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            $this->customer = $customer;
            $this->fill($customer->only(['name', 'company', 'email', 'phone', 'billing_address', 'notes']));
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->customer) {
            $this->customer->update($data);
            $message = 'مشتری به‌روزرسانی شد.';
            $customer = $this->customer;
        } else {
            $customer = Customer::create($data);
            $message = 'مشتری ایجاد شد.';
        }

        session()->flash('status', $message);

        return $this->redirectRoute('customers.show', $customer, navigate: true);
    }

    public function render()
    {
        return view('livewire.customers.form')
            ->title($this->customer ? 'ویرایش مشتری' : 'مشتری جدید');
    }
}
