<div class="mx-auto max-w-2xl">
    <form wire:submit="save" class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">نام *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">شرکت</label>
                <input type="text" wire:model="company" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">ایمیل</label>
                <input type="email" wire:model="email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">تلفن</label>
                <input type="text" wire:model="phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-stone-700">آدرس</label>
            <textarea wire:model="address" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-stone-700">یادداشت‌ها</label>
            <textarea wire:model="notes" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-4">
            <a href="{{ route('suppliers.index') }}" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">لغو</a>
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">ذخیره تأمین‌کننده</button>
        </div>
    </form>
</div>
