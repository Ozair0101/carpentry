<div class="mx-auto max-w-4xl">
    <form wire:submit="save" class="space-y-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-stone-700">مشتری *</label>
                    <select wire:model="customer_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">— انتخاب مشتری —</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">وضعیت</label>
                    <select wire:model="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @foreach (\App\Models\Estimate::STATUSES as $s)
                            <option value="{{ $s }}">{{ \App\Support\Labels::get($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ صدور *</label>
                    <input type="date" wire:model="issue_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('issue_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">معتبر تا</label>
                    <input type="date" wire:model="valid_until" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-stone-800">اقلام</h3>
            @include('partials.line-items')
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">یادداشت‌ها (نمایش روی برآورد)</label>
                    <textarea wire:model="notes" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">شرایط</label>
                    <textarea wire:model="terms" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('estimates.index') }}" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</a>
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">ذخیره برآورد</button>
        </div>
    </form>
</div>
