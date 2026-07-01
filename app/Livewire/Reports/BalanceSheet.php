<?php

namespace App\Livewire\Reports;

use App\Support\Reports;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class BalanceSheet extends Component
{
    #[Url]
    public string $asOf = '';

    public function mount(): void
    {
        if (! $this->asOf) {
            $this->asOf = Carbon::today()->format('Y-m-d');
        }
    }

    public function render()
    {
        $asOf = Carbon::parse($this->asOf)->endOfDay();

        return view('livewire.reports.balance-sheet', [
            'report' => Reports::balanceSheet($asOf),
        ])->title('بیلانس');
    }
}
