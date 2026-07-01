<?php

namespace App\Livewire;

use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $now = Carbon::now();

        $activeJobs = Project::whereIn('status', ['scheduled', 'in_progress', 'on_hold'])->count();

        $jobsDueThisWeek = Project::whereIn('status', ['scheduled', 'in_progress'])
            ->whereBetween('due_date', [$now->copy()->startOfDay(), $now->copy()->addDays(7)->endOfDay()])
            ->with('customer')
            ->orderBy('due_date')
            ->get();

        $openEstimates = Estimate::whereIn('status', ['draft', 'sent'])
            ->with('customer')->latest()->take(6)->get();

        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->with('customer')->orderBy('due_date')->get();

        $outstanding = $unpaidInvoices->sum(fn ($i) => $i->balance());

        $revenueThisMonth = Invoice::query()
            ->whereYear('issue_date', $now->year)
            ->whereMonth('issue_date', $now->month)
            ->sum('amount_paid');

        return view('livewire.dashboard', [
            'activeJobs' => $activeJobs,
            'openEstimatesCount' => Estimate::whereIn('status', ['draft', 'sent'])->count(),
            'unpaidCount' => $unpaidInvoices->count(),
            'outstanding' => $outstanding,
            'revenueThisMonth' => $revenueThisMonth,
            'jobsDueThisWeek' => $jobsDueThisWeek,
            'openEstimates' => $openEstimates,
            'unpaidInvoices' => $unpaidInvoices->take(6),
        ])->title('Dashboard');
    }
}
