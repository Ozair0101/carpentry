<div class="mx-auto max-w-2xl">
    <form wire:submit="save" class="space-y-6">
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-stone-800">مشخصات شرکت</h3>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">نام شرکت *</label>
                <input type="text" wire:model="company_name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">آدرس</label>
                <textarea wire:model="address" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">تلفن</label>
                    <input type="text" wire:model="phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">ایمیل</label>
                    <input type="email" wire:model="email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">شماره مالیاتی</label>
                    <input type="text" wire:model="tax_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-stone-800">پیش‌فرض‌های صورت‌حساب</h3>
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">واحد پول</label>
                    <select wire:model="currency" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @foreach (['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'NZD', 'INR', 'PKR', 'AED', 'ZAR'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">نرخ مالیات پیش‌فرض ٪</label>
                    <input type="number" step="0.01" wire:model="tax_rate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('tax_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">پیشوند شماره برآورد</label>
                    <input type="text" wire:model="estimate_prefix" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">پیشوند شماره بل</label>
                    <input type="text" wire:model="invoice_prefix" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">شرایط پیش‌فرض (برآوردها و بل‌ها)</label>
                <textarea wire:model="default_terms" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <x-save-button label="ذخیره تنظیمات" busy="در حال ذخیره تنظیمات…" />
        </div>
    </form>
</div>
