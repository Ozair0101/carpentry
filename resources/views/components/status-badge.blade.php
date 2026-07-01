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
    $labels = [
        'draft' => 'پیش‌نویس',
        'sent' => 'ارسال‌شده',
        'approved' => 'تأییدشده',
        'rejected' => 'ردشده',
        'lead' => 'سرنخ',
        'scheduled' => 'زمان‌بندی‌شده',
        'in_progress' => 'در حال انجام',
        'on_hold' => 'معلق',
        'completed' => 'تکمیل‌شده',
        'cancelled' => 'لغوشده',
        'partial' => 'پرداخت جزئی',
        'paid' => 'پرداخت‌شده',
        'overdue' => 'سررسید گذشته',
    ];
    $classes = $map[$status] ?? 'bg-stone-100 text-stone-600';
    $label = $labels[$status] ?? str_replace('_', ' ', $status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium $classes"]) }}>
    {{ $label }}
</span>
