<div class="mx-auto max-w-3xl">
    <form wire:submit="save" class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">مشتری *</label>
                <select wire:model="customer_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    <option value="">— انتخاب مشتری —</option>
                    @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                @error('customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">وضعیت</label>
                <select wire:model="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach (\App\Models\Project::STATUSES as $s)<option value="{{ $s }}">{{ \App\Support\Labels::get($s) }}</option>@endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-stone-700">عنوان *</label>
                <input type="text" wire:model="title" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-stone-700">توضیحات</label>
                <textarea wire:model="description" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-stone-700">آدرس محل کار</label>
                <textarea wire:model="site_address" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ شروع</label>
                <input type="date" wire:model="start_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">تاریخ سررسید</label>
                <input type="date" wire:model="due_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">واگذار شده به</label>
                <input type="text" wire:model="assigned_to" placeholder="مثلاً: احمد (سرگروه)" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">بودجه</label>
                <input type="number" step="0.01" wire:model="budget" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-4">
            <a href="{{ route('jobs.index') }}" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</a>
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">ذخیره کار</button>
        </div>
    </form>
</div>
