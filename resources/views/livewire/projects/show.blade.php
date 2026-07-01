<?php use App\Support\Format; ?>
<div class="space-y-6" x-data="{ tab: 'details' }">
    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-stone-800">{{ $project->title }}</h2>
                <x-status-badge :status="$project->status" />
            </div>
            <p class="text-stone-500">
                <a href="{{ route('customers.show', $project->customer) }}" wire:navigate class="hover:text-amber-700">{{ $project->customer->name }}</a>
                @if ($project->due_date) · due {{ $project->due_date->format('d M Y') }}@endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <select wire:change="setStatus($event.target.value)" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm">
                @foreach (\App\Models\Project::STATUSES as $s)<option value="{{ $s }}" @selected($project->status === $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>@endforeach
            </select>
            <button wire:click="createInvoice" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">Create invoice</button>
            <a href="{{ route('jobs.edit', $project) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">Edit</a>
            <button wire:click="delete" wire:confirm="Delete this job and all its costs?" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-400 hover:text-red-600">Delete</button>
        </div>
    </div>

    {{-- Cost summary card --}}
    <div class="grid grid-cols-2 gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-4">
        <div>
            <p class="text-sm text-stone-500">Budget (quoted)</p>
            <p class="mt-1 text-2xl font-bold text-stone-800">{{ Format::money($budget) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">Actual cost</p>
            <p class="mt-1 text-2xl font-bold text-stone-800">{{ Format::money($actual) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">Margin</p>
            <p class="mt-1 text-2xl font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ Format::money($margin) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">Margin %</p>
            <p class="mt-1 text-2xl font-bold {{ ($marginPct ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $marginPct !== null ? $marginPct.'%' : '—' }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-stone-200">
        <nav class="flex gap-6 text-sm font-medium">
            @foreach (['details' => 'Details', 'tasks' => 'Tasks', 'costs' => 'Costs', 'schedule' => 'Schedule'] as $key => $label)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-amber-600 text-amber-700' : 'border-transparent text-stone-500 hover:text-stone-700'"
                        class="border-b-2 px-1 pb-3">{{ $label }}</button>
            @endforeach
        </nav>
    </div>

    {{-- Details --}}
    <div x-show="tab === 'details'" class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="mb-3 font-semibold text-stone-800">Job details</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-stone-400">Description</dt><dd class="whitespace-pre-line text-stone-700">{{ $project->description ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Site address</dt><dd class="whitespace-pre-line text-stone-700">{{ $project->site_address ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Assigned to</dt><dd class="text-stone-700">{{ $project->assigned_to ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Dates</dt><dd class="text-stone-700">{{ $project->start_date?->format('d M Y') ?: '—' }} → {{ $project->due_date?->format('d M Y') ?: '—' }}</dd></div>
            </dl>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="mb-3 font-semibold text-stone-800">Linked documents</h3>
            @if ($project->estimate)
                <a href="{{ route('estimates.show', $project->estimate) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 hover:text-amber-700">
                    <span class="text-sm">Estimate {{ $project->estimate->number }}</span><span class="text-sm">{{ Format::money($project->estimate->total) }}</span>
                </a>
            @endif
            @forelse ($project->invoices as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                    <span class="text-sm">Invoice {{ $invoice->number }}</span>
                    <span class="flex items-center gap-2 text-sm">{{ Format::money($invoice->total) }} <x-status-badge :status="$invoice->status" /></span>
                </a>
            @empty
                @if (! $project->estimate)<p class="text-sm text-stone-400">No linked documents.</p>@endif
            @endforelse
        </div>
    </div>

    {{-- Tasks --}}
    <div x-show="tab === 'tasks'" class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" style="display:none">
        <form wire:submit="addTask" class="mb-4 flex gap-2">
            <input type="text" wire:model="newTask" placeholder="Add a task…" class="flex-1 rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Add</button>
        </form>
        @error('newTask') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror
        <div class="space-y-1">
            @forelse ($project->tasks as $task)
                <div class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-stone-50">
                    <input type="checkbox" @checked($task->is_done) wire:click="toggleTask({{ $task->id }})" class="rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                    <span class="flex-1 text-sm {{ $task->is_done ? 'text-stone-400 line-through' : 'text-stone-700' }}">{{ $task->title }}</span>
                    <button wire:click="deleteTask({{ $task->id }})" class="text-stone-300 hover:text-red-600">✕</button>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">No tasks yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Costs --}}
    <div x-show="tab === 'costs'" class="space-y-4" style="display:none">
        <form wire:submit="addExpense" class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Type</label>
                <select wire:model="expType" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                    @foreach (\App\Models\ProjectExpense::TYPES as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-stone-600">Description</label>
                <input type="text" wire:model="expDescription" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('expDescription') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Qty</label>
                <input type="number" step="0.01" wire:model="expQty" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Unit cost</label>
                <input type="number" step="0.01" wire:model="expUnitCost" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
            </div>
            <div class="sm:col-span-5">
                <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ Log cost</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Description</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3 text-right">Unit</th><th class="px-4 py-3 text-right">Total</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($project->expenses as $expense)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-2 text-stone-500">{{ $expense->incurred_on->format('d M') }}</td>
                            <td class="px-4 py-2"><span class="capitalize text-stone-600">{{ $expense->type }}</span></td>
                            <td class="px-4 py-2 text-stone-700">{{ $expense->description }}</td>
                            <td class="px-4 py-2 text-right text-stone-600">{{ rtrim(rtrim(number_format($expense->qty, 2), '0'), '.') }}</td>
                            <td class="px-4 py-2 text-right text-stone-600">{{ Format::money($expense->unit_cost) }}</td>
                            <td class="px-4 py-2 text-right font-medium">{{ Format::money($expense->total) }}</td>
                            <td class="px-4 py-2 text-right"><button wire:click="deleteExpense({{ $expense->id }})" class="text-stone-300 hover:text-red-600">✕</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-stone-400">No costs logged yet.</td></tr>
                    @endforelse
                </tbody>
                @if ($project->expenses->isNotEmpty())
                    <tfoot class="bg-stone-50 font-semibold">
                        <tr><td colspan="5" class="px-4 py-2 text-right">Total actual cost</td><td class="px-4 py-2 text-right">{{ Format::money($actual) }}</td><td></td></tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Schedule --}}
    <div x-show="tab === 'schedule'" class="space-y-4" style="display:none">
        <form wire:submit="addAppointment" class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-stone-600">Title</label>
                <input type="text" wire:model="apptTitle" placeholder="e.g. Install on site" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">Start</label>
                <input type="datetime-local" wire:model="apptStart" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptStart') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">End</label>
                <input type="datetime-local" wire:model="apptEnd" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptEnd') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-4"><button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ Add appointment</button></div>
        </form>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            @forelse ($project->appointments as $appt)
                <div class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $appt->title }}</p>
                        <p class="text-xs text-stone-500">{{ $appt->starts_at->format('D d M, H:i') }} – {{ $appt->ends_at->format('H:i') }}</p>
                    </div>
                    <button wire:click="deleteAppointment({{ $appt->id }})" class="text-stone-300 hover:text-red-600">✕</button>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">No appointments scheduled.</p>
            @endforelse
        </div>
    </div>
</div>
