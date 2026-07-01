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
                @if ($project->due_date) · سررسید {{ $project->due_date->translatedFormat('d M Y') }}@endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <select wire:change="setStatus($event.target.value)" class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm">
                @foreach (\App\Models\Project::STATUSES as $s)<option value="{{ $s }}" @selected($project->status === $s)>{{ \App\Support\Labels::get($s) }}</option>@endforeach
            </select>
            <button wire:click="createInvoice" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">ایجاد فاکتور</button>
            <a href="{{ route('jobs.edit', $project) }}" wire:navigate class="rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700">ویرایش</a>
            <button wire:click="delete" wire:confirm="این کار و همه هزینه‌های آن حذف شود؟" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-400 hover:text-red-600">حذف</button>
        </div>
    </div>

    {{-- Cost summary card --}}
    <div class="grid grid-cols-2 gap-4 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-4">
        <div>
            <p class="text-sm text-stone-500">بودجه (پیشنهادی)</p>
            <p class="mt-1 text-2xl font-bold text-stone-800">{{ Format::money($budget) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">هزینه واقعی</p>
            <p class="mt-1 text-2xl font-bold text-stone-800">{{ Format::money($actual) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">سود</p>
            <p class="mt-1 text-2xl font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ Format::money($margin) }}</p>
        </div>
        <div>
            <p class="text-sm text-stone-500">درصد سود</p>
            <p class="mt-1 text-2xl font-bold {{ ($marginPct ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $marginPct !== null ? $marginPct.'%' : '—' }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-stone-200">
        <nav class="flex gap-6 text-sm font-medium">
            @foreach (['details' => 'جزئیات', 'tasks' => 'وظایف', 'costs' => 'هزینه‌ها', 'schedule' => 'زمان‌بندی'] as $key => $label)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-amber-600 text-amber-700' : 'border-transparent text-stone-500 hover:text-stone-700'"
                        class="border-b-2 px-1 pb-3">{{ $label }}</button>
            @endforeach
        </nav>
    </div>

    {{-- Details --}}
    <div x-show="tab === 'details'" class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="mb-3 font-semibold text-stone-800">جزئیات کار</h3>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-stone-400">توضیحات</dt><dd class="whitespace-pre-line text-stone-700">{{ $project->description ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">آدرس محل کار</dt><dd class="whitespace-pre-line text-stone-700">{{ $project->site_address ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">واگذار شده به</dt><dd class="text-stone-700">{{ $project->assigned_to ?: '—' }}</dd></div>
                <div><dt class="text-stone-400">تاریخ‌ها</dt><dd class="text-stone-700">{{ $project->start_date?->translatedFormat('d M Y') ?: '—' }} → {{ $project->due_date?->translatedFormat('d M Y') ?: '—' }}</dd></div>
            </dl>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="mb-3 font-semibold text-stone-800">اسناد مرتبط</h3>
            @if ($project->estimate)
                <a href="{{ route('estimates.show', $project->estimate) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 hover:text-amber-700">
                    <span class="text-sm">برآورد {{ $project->estimate->number }}</span><span class="text-sm">{{ Format::money($project->estimate->total) }}</span>
                </a>
            @endif
            @forelse ($project->invoices as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0 hover:text-amber-700">
                    <span class="text-sm">فاکتور {{ $invoice->number }}</span>
                    <span class="flex items-center gap-2 text-sm">{{ Format::money($invoice->total) }} <x-status-badge :status="$invoice->status" /></span>
                </a>
            @empty
                @if (! $project->estimate)<p class="text-sm text-stone-400">سند مرتبطی وجود ندارد.</p>@endif
            @endforelse
        </div>
    </div>

    {{-- Tasks --}}
    <div x-show="tab === 'tasks'" class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" style="display:none">
        <form wire:submit="addTask" class="mb-4 flex gap-2">
            <input type="text" wire:model="newTask" placeholder="افزودن وظیفه…" class="flex-1 rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">افزودن</button>
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
                <p class="py-4 text-center text-sm text-stone-400">هنوز وظیفه‌ای وجود ندارد.</p>
            @endforelse
        </div>
    </div>

    {{-- Costs --}}
    <div x-show="tab === 'costs'" class="space-y-4" style="display:none">
        <form wire:submit="addExpense" class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">نوع</label>
                <select wire:model="expType" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                    @foreach (\App\Models\ProjectExpense::TYPES as $t)<option value="{{ $t }}">{{ \App\Support\Labels::get($t) }}</option>@endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-stone-600">توضیحات</label>
                <input type="text" wire:model="expDescription" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('expDescription') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">مقدار</label>
                <input type="number" step="0.01" wire:model="expQty" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">قیمت واحد</label>
                <input type="number" step="0.01" wire:model="expUnitCost" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
            </div>
            <div class="sm:col-span-5">
                <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ ثبت هزینه</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">
                    <tr><th class="px-4 py-3">تاریخ</th><th class="px-4 py-3">نوع</th><th class="px-4 py-3">توضیحات</th><th class="px-4 py-3 text-left">مقدار</th><th class="px-4 py-3 text-left">واحد</th><th class="px-4 py-3 text-left">مجموع</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($project->expenses as $expense)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-2 text-stone-500">{{ $expense->incurred_on->translatedFormat('d M') }}</td>
                            <td class="px-4 py-2"><span class="capitalize text-stone-600">{{ $expense->type }}</span></td>
                            <td class="px-4 py-2 text-stone-700">{{ $expense->description }}</td>
                            <td class="px-4 py-2 text-left text-stone-600">{{ rtrim(rtrim(number_format($expense->qty, 2), '0'), '.') }}</td>
                            <td class="px-4 py-2 text-left text-stone-600">{{ Format::money($expense->unit_cost) }}</td>
                            <td class="px-4 py-2 text-left font-medium">{{ Format::money($expense->total) }}</td>
                            <td class="px-4 py-2 text-left"><button wire:click="deleteExpense({{ $expense->id }})" class="text-stone-300 hover:text-red-600">✕</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-stone-400">هنوز هزینه‌ای ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
                @if ($project->expenses->isNotEmpty())
                    <tfoot class="bg-stone-50 font-semibold">
                        <tr><td colspan="5" class="px-4 py-2 text-left">مجموع هزینه واقعی</td><td class="px-4 py-2 text-left">{{ Format::money($actual) }}</td><td></td></tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Schedule --}}
    <div x-show="tab === 'schedule'" class="space-y-4" style="display:none">
        <form wire:submit="addAppointment" class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-stone-600">عنوان</label>
                <input type="text" wire:model="apptTitle" placeholder="مثلاً: نصب در محل" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptTitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">شروع</label>
                <input type="datetime-local" wire:model="apptStart" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptStart') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-stone-600">پایان</label>
                <input type="datetime-local" wire:model="apptEnd" class="w-full rounded-lg border border-stone-300 px-2 py-1.5 text-sm">
                @error('apptEnd') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-4"><button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ افزودن قرار ملاقات</button></div>
        </form>

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            @forelse ($project->appointments as $appt)
                <div class="flex items-center justify-between border-b border-stone-100 py-2 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $appt->title }}</p>
                        <p class="text-xs text-stone-500">{{ $appt->starts_at->translatedFormat('D d M, H:i') }} – {{ $appt->ends_at->format('H:i') }}</p>
                    </div>
                    <button wire:click="deleteAppointment({{ $appt->id }})" class="text-stone-300 hover:text-red-600">✕</button>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-stone-400">هیچ قرار ملاقاتی زمان‌بندی نشده است.</p>
            @endforelse
        </div>
    </div>
</div>
