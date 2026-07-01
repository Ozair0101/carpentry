<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Invoice::query()
            ->with('customer')
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('number', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$this->search}%"));
            }));

        $invoices = (clone $query)->latest()->paginate(15);

        $outstanding = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->get()->sum(fn ($i) => $i->balance());

        return view('livewire.invoices.index', [
            'invoices' => $invoices,
            'outstanding' => $outstanding,
        ])->title('فاکتورها');
    }
}
