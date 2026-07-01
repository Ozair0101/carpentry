<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
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
        $query = Purchase::query()
            ->with('supplier')
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('reference', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$this->search}%"));
            }));

        $bills = (clone $query)->latest('bill_date')->paginate(15);

        $totalPayable = Purchase::whereIn('status', ['unpaid', 'partial'])
            ->get()->sum(fn (Purchase $p) => $p->balance());

        return view('livewire.purchases.index', [
            'bills' => $bills,
            'totalPayable' => $totalPayable,
        ])->title('Bills (payables)');
    }
}
