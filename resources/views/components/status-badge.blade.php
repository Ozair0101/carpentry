@props(['status'])

@php
    $map = [
        // estimates
        'draft' => 'bg-stone-100 text-stone-600',
        'sent' => 'bg-blue-100 text-blue-700',
        'approved' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        // jobs
        'lead' => 'bg-purple-100 text-purple-700',
        'scheduled' => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-amber-100 text-amber-700',
        'on_hold' => 'bg-orange-100 text-orange-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
        // invoices
        'partial' => 'bg-amber-100 text-amber-700',
        'paid' => 'bg-green-100 text-green-700',
        'overdue' => 'bg-red-100 text-red-700',
    ];
    $classes = $map[$status] ?? 'bg-stone-100 text-stone-600';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize $classes"]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
