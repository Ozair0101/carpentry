<?php

namespace App\Livewire\Reports;

use App\Support\Reports;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class IncomeStatement extends Component
{
    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public function mount(): void
    {
        if (! $this->from) {
            $this->from = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (! $this->to) {
            $this->to = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
    }

    /** Quick presets: this month, last month, this year. */
    public function preset(string $key): void
    {
        $now = Carbon::now();

        [$from, $to] = match ($key) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        $this->from = $from->format('Y-m-d');
        $this->to = $to->format('Y-m-d');
    }

    public function render()
    {
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        // Guard against a reversed range.
        if ($to->lt($from)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return view('livewire.reports.income-statement', [
            'report' => Reports::incomeStatement($from, $to),
        ])->title('صورت سود و زیان');
    }
}
