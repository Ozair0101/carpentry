<?php use App\Support\Format; ?>
<div class="mx-auto max-w-4xl space-y-6">
    {{-- Controls --}}
    <div class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white p-4 shadow-sm print:hidden">
        <div class="flex items-end gap-2">
            <label class="text-xs font-medium text-stone-500">سال</label>
            <select wire:model.live="year" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                @foreach ($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button onclick="window.print()" class="rounded-lg bg-stone-800 px-3 py-2 text-xs font-semibold text-white hover:bg-stone-900">🖨 چاپ</button>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">عواید سال {{ $report['year'] }}</p>
            <p class="mt-1 text-xl font-bold text-green-600">{{ Format::money($report['incomeTotal']) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">مصارف سال</p>
            <p class="mt-1 text-xl font-bold text-red-600">{{ Format::money($report['expenseTotal']) }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-stone-500">مفاد خالص سال</p>
            <p class="mt-1 text-xl font-bold {{ $report['netTotal'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ Format::money($report['netTotal']) }}</p>
        </div>
    </div>

    {{-- Monthly table --}}
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 p-5 text-center">
            <h2 class="text-lg font-bold text-stone-800">گزارش ماهانه {{ $report['year'] }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-right text-xs uppercase tracking-wide text-stone-400">
                    <tr class="border-b border-stone-100">
                        <th class="px-4 py-3">ماه</th>
                        <th class="px-4 py-3 text-left">عواید</th>
                        <th class="px-4 py-3 text-left">مصارف</th>
                        <th class="px-4 py-3 text-left">مفاد خالص</th>
                        <th class="hidden px-4 py-3 sm:table-cell">مقایسه</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($report['months'] as $row)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-2.5 font-medium text-stone-700">{{ $row['label'] }}</td>
                            <td class="px-4 py-2.5 text-left text-green-600">{{ Format::money($row['income']) }}</td>
                            <td class="px-4 py-2.5 text-left text-red-600">{{ Format::money($row['expense']) }}</td>
                            <td class="px-4 py-2.5 text-left font-medium {{ $row['net'] >= 0 ? 'text-stone-800' : 'text-red-600' }}">{{ Format::money($row['net']) }}</td>
                            <td class="hidden px-4 py-2.5 sm:table-cell">
                                <div class="space-y-1">
                                    <div class="h-1.5 rounded-full bg-green-500" style="width: {{ round($row['income'] / $report['peak'] * 100) }}%"></div>
                                    <div class="h-1.5 rounded-full bg-red-400" style="width: {{ round($row['expense'] / $report['peak'] * 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-stone-200 font-bold text-stone-800">
                        <td class="px-4 py-3">مجموع</td>
                        <td class="px-4 py-3 text-left text-green-700">{{ Format::money($report['incomeTotal']) }}</td>
                        <td class="px-4 py-3 text-left text-red-600">{{ Format::money($report['expenseTotal']) }}</td>
                        <td class="px-4 py-3 text-left {{ $report['netTotal'] >= 0 ? 'text-green-700' : 'text-red-600' }}">{{ Format::money($report['netTotal']) }}</td>
                        <td class="hidden sm:table-cell"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
