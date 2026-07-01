<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $deletingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Customer::findOrFail($this->deletingId)->delete();
            session()->flash('status', 'Customer deleted.');
        }
        $this->deletingId = null;
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('company', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->withCount(['projects', 'invoices'])
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.customers.index', ['customers' => $customers])
            ->title('Customers');
    }
}
