<?php

namespace App\Livewire\Reports;

use App\Support\Reports;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class Monthly extends Component
{
    #[Url]
    public int $year = 0;

    public function mount(): void
    {
        if (! $this->year) {
            $this->year = (int) Carbon::now()->year;
        }
    }

    public function render()
    {
        $current = (int) Carbon::now()->year;
        $years = range($current, $current - 5);

        return view('livewire.reports.monthly', [
            'report' => Reports::monthly($this->year),
            'years' => $years,
        ])->title('گزارش ماهانه');
    }
}
