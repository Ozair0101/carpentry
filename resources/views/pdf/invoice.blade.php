<?php use App\Support\Format; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { color: #292524; font-size: 12px; margin: 0; direction: rtl; font-family: 'DejaVu Sans', sans-serif; }
        .head { display: flex; justify-content: space-between; border-bottom: 2px solid #d97706; padding-bottom: 16px; }
        .company { font-size: 20px; font-weight: bold; color: #292524; }
        .muted { color: #78716c; }
        .doc-title { font-size: 26px; font-weight: bold; text-align: left; color: #d97706; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { text-align: right; font-size: 10px; text-transform: uppercase; color: #78716c; border-bottom: 1px solid #e7e5e4; padding: 6px 4px; }
        td { padding: 8px 4px; border-bottom: 1px solid #f5f5f4; }
        .right { text-align: left; }
        .totals { margin-top: 16px; width: 260px; float: left; }
        .totals td { border: none; padding: 3px 4px; }
        .grand { font-size: 15px; font-weight: bold; border-top: 2px solid #292524 !important; }
        .due { color: #dc2626; font-weight: bold; }
        .section { clear: both; padding-top: 24px; color: #78716c; }
    </style>
</head>
<body>
    <div class="head">
        <div>
            <div class="company">{{ $settings->company_name }}</div>
            <div class="muted" style="white-space: pre-line;">{{ $settings->address }}</div>
            <div class="muted">{{ $settings->phone }} @if($settings->phone && $settings->email) · @endif {{ $settings->email }}</div>
            @if ($settings->tax_id)<div class="muted">شناسه مالیاتی: {{ $settings->tax_id }}</div>@endif
        </div>
        <div>
            <div class="doc-title">فاکتور</div>
            <div class="right muted">{{ $invoice->number }}</div>
            <div class="right muted">تاریخ صدور {{ $invoice->issue_date->translatedFormat('d M Y') }}</div>
            @if ($invoice->due_date)<div class="right muted">تاریخ سررسید {{ $invoice->due_date->translatedFormat('d M Y') }}</div>@endif
        </div>
    </div>

    <div style="margin-top: 20px;">
        <div class="muted" style="font-size: 10px; text-transform: uppercase;">صورت‌حساب برای</div>
        <div style="font-weight: bold;">{{ $invoice->customer->name }}</div>
        @if ($invoice->customer->company)<div class="muted">{{ $invoice->customer->company }}</div>@endif
        <div class="muted" style="white-space: pre-line;">{{ $invoice->customer->billing_address }}</div>
    </div>

    <table>
        <thead>
            <tr><th>توضیحات</th><th class="right">تعداد</th><th class="right">قیمت واحد</th><th class="right">مجموع</th></tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }} {{ $item->unit }}</td>
                    <td class="right">{{ Format::money($item->unit_price) }}</td>
                    <td class="right">{{ Format::money($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">جمع جزء</td><td class="right">{{ Format::money($invoice->subtotal) }}</td></tr>
        @if ($invoice->discount > 0)<tr><td class="muted">تخفیف</td><td class="right">−{{ Format::money($invoice->discount) }}</td></tr>@endif
        <tr><td class="muted">مالیات ({{ rtrim(rtrim(number_format($invoice->tax_rate, 2), '0'), '.') }}%)</td><td class="right">{{ Format::money($invoice->tax_total) }}</td></tr>
        <tr class="grand"><td>مجموع</td><td class="right">{{ Format::money($invoice->total) }}</td></tr>
        <tr><td class="muted">پرداخت‌شده</td><td class="right">{{ Format::money($invoice->amount_paid) }}</td></tr>
        <tr><td class="due">مانده قابل پرداخت</td><td class="right due">{{ Format::money($invoice->balance()) }}</td></tr>
    </table>

    @if ($invoice->notes)<div class="section"><strong>یادداشت‌ها:</strong> {{ $invoice->notes }}</div>@endif
    @if ($invoice->terms)<div class="section"><strong>شرایط:</strong> {{ $invoice->terms }}</div>@endif
</body>
</html>
