<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Central Dari (Afghan Persian) labels for enum values (statuses, payment
 * methods, account/expense/salary types). Keeps dropdown option text and
 * badges consistent across the app. Stored DB values stay English; only the
 * displayed label is translated here. Falls back to a humanised version of the
 * key when no translation is defined.
 */
class Labels
{
    public const MAP = [
        // Estimate / invoice / job / purchase statuses
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
        'unpaid' => 'پرداخت‌نشده',
        'pending' => 'در انتظار',

        // Account / payment methods
        'cash' => 'نقده',
        'bank' => 'بانک',
        'card' => 'کارت',
        'cheque' => 'چک',
        'other' => 'سایر',

        // Project expense types
        'material' => 'مواد',
        'labour' => 'دستمزد',
        'subcontractor' => 'قرارداد فرعی',

        // Salary types
        'monthly' => 'ماهانه',
        'daily' => 'روزانه',
        'hourly' => 'ساعتی',
    ];

    public static function get(?string $key): string
    {
        if ($key === null || $key === '') {
            return '';
        }

        return self::MAP[$key] ?? Str::headline($key);
    }
}
