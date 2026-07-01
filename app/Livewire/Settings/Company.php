<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class Company extends Component
{
    public Setting $settings;

    public string $company_name = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $tax_id = '';

    public string $currency = 'USD';

    public $tax_rate = 0;

    public string $estimate_prefix = 'EST-';

    public string $invoice_prefix = 'INV-';

    public string $default_terms = '';

    public function mount(): void
    {
        $this->settings = Setting::current();
        $this->company_name = $this->settings->company_name;
        $this->address = (string) $this->settings->address;
        $this->phone = (string) $this->settings->phone;
        $this->email = (string) $this->settings->email;
        $this->tax_id = (string) $this->settings->tax_id;
        $this->currency = $this->settings->currency;
        $this->tax_rate = $this->settings->tax_rate;
        $this->estimate_prefix = $this->settings->estimate_prefix;
        $this->invoice_prefix = $this->settings->invoice_prefix;
        $this->default_terms = (string) $this->settings->default_terms;
    }

    public function save(): void
    {
        $data = $this->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'tax_id' => 'nullable|string|max:100',
            'currency' => 'required|string|size:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'estimate_prefix' => 'required|string|max:20',
            'invoice_prefix' => 'required|string|max:20',
            'default_terms' => 'nullable|string',
        ]);

        $this->settings->update($data);

        session()->flash('status', 'Settings saved.');
    }

    public function render()
    {
        return view('livewire.settings.company')->title('Settings');
    }
}
