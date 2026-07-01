<?php use App\Support\Format; ?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">{{ $customer->name }}</h2>
            @if ($customer->company)<p class="text-stone-500">{{ $customer->company }}</p>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('estimates.create', ['customer' => $customer->id]) }}" wire:navigate class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">+ Estimate</a>
            <a href="{{ route('jobs.create', ['customer' => $customer->id]) }}" wire:navigate class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">+ Job</a>
            <a href="{{ route('customers.edit', $customer) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">Edit</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Contact card --}}
        <div class="space-y-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h3 class="font-semibold text-stone-800">Contact</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-stone-400">Phone</dt><dd class="text-stone-700">{{ $customer->phone ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Email</dt><dd class="text-stone-700">{{ $customer->email ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">Billing address</dt><dd class="whitespace-pre-line text-stone-700">{{ $customer->billing_address ?: '—' }}</dd></div>
                @if ($customer->notes)
                    <div><dt class="text-stone-400">Notes</dt><dd class="whitespace-pre-line text-stone-700">{{ $customer->notes }}</dd></div>
                @endif
            </dl>
        </div>

        {{-- History --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-semibold text-stone-800">Jobs</h3>
                @forelse ($customer->projects as $job)
                    <a href="{{ route('jobs.show', $job) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                        <span class="text-sm">{{ $job->title }}</span>
                        <x-status-badge :status="$job->status" />
                    </a>
                @empty
                    <p class="text-sm text-stone-400">No jobs yet.</p>
                @endforelse
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-semibold text-stone-800">Estimates</h3>
                @forelse ($customer->estimates as $estimate)
                    <a href="{{ route('estimates.show', $estimate) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                        <span class="text-sm">{{ $estimate->number }} · {{ $estimate->issue_date->format('d M Y') }}</span>
                        <span class="flex items-center gap-3 text-sm"><span>{{ Format::money($estimate->total) }}</span><x-status-badge :status="$estimate->status" /></span>
                    </a>
                @empty
                    <p class="text-sm text-stone-400">No estimates yet.</p>
                @endforelse
            </div>

            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-semibold text-stone-800">Invoices</h3>
                @forelse ($customer->invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                        <span class="text-sm">{{ $invoice->number }} · {{ $invoice->issue_date->format('d M Y') }}</span>
                        <span class="flex items-center gap-3 text-sm"><span>{{ Format::money($invoice->total) }}</span><x-status-badge :status="$invoice->status" /></span>
                    </a>
                @empty
                    <p class="text-sm text-stone-400">No invoices yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
