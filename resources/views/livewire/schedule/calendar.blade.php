<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <button wire:click="goToMonth(-1)" class="rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm hover:bg-stone-50">→</button>
            <button wire:click="today" class="rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm hover:bg-stone-50">امروز</button>
            <button wire:click="goToMonth(1)" class="rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm hover:bg-stone-50">←</button>
            <h2 class="mr-2 text-lg font-semibold text-stone-800">{{ $cursor->translatedFormat('F Y') }}</h2>
        </div>
        <button wire:click="openForm" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">+ قرار ملاقات</button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="grid grid-cols-7 border-b border-stone-200 bg-stone-50 text-center text-xs font-semibold uppercase tracking-wide text-stone-500">
            @foreach (['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'] as $dow)
                <div class="py-2">{{ $dow }}</div>
            @endforeach
        </div>
        <div class="grid grid-cols-7">
            @foreach ($days as $day)
                @php
                    $inMonth = $day->month === $cursor->month;
                    $isToday = $day->isToday();
                    $dayAppts = $appointments->get($day->format('Y-m-d'), collect());
                @endphp
                <div class="min-h-28 border-b border-r border-stone-100 p-1.5 {{ $inMonth ? 'bg-white' : 'bg-stone-50/60' }}"
                     wire:click="openForm('{{ $day->format('Y-m-d') }}')">
                    <div class="mb-1 flex justify-end">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs {{ $isToday ? 'bg-amber-600 font-bold text-white' : ($inMonth ? 'text-stone-600' : 'text-stone-400') }}">{{ $day->day }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($dayAppts as $appt)
                            <div wire:click.stop
                                 class="group flex items-center justify-between gap-1 rounded bg-amber-100 px-1.5 py-1 text-xs text-amber-800">
                                <span class="truncate">
                                    <span class="font-medium">{{ $appt->starts_at->format('H:i') }}</span>
                                    @if ($appt->project)
                                        <a href="{{ route('jobs.show', $appt->project) }}" wire:navigate class="hover:underline">{{ $appt->title }}</a>
                                    @else
                                        {{ $appt->title }}
                                    @endif
                                </span>
                                <button wire:click.stop="deleteAppointment({{ $appt->id }})" class="hidden text-amber-500 hover:text-red-600 group-hover:block">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Add appointment modal --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-stone-900/50 p-4">
            <form wire:submit="save" class="my-4 max-h-[90vh] w-full max-w-sm space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">قرار ملاقات جدید</h3>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">عنوان</label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">کار (اختیاری)</label>
                    <select wire:model="project_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                        <option value="">— هیچ‌کدام —</option>
                        @foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">شروع</label>
                        <input type="datetime-local" wire:model="starts_at" class="w-full rounded-lg border border-stone-300 px-2 py-2 text-sm">
                        @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">پایان</label>
                        <input type="datetime-local" wire:model="ends_at" class="w-full rounded-lg border border-stone-300 px-2 py-2 text-sm">
                        @error('ends_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showForm', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</button>
                    <x-save-button class="!px-4" />
                </div>
            </form>
        </div>
    @endif
</div>
