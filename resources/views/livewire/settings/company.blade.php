<div class="mx-auto max-w-2xl">
    <form wire:submit="save" class="space-y-6">
        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-stone-800">Company profile</h3>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Company name *</label>
                <input type="text" wire:model="company_name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Address</label>
                <textarea wire:model="address" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Phone</label>
                    <input type="text" wire:model="phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Email</label>
                    <input type="email" wire:model="email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Tax ID</label>
                    <input type="text" wire:model="tax_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-stone-800">Billing defaults</h3>
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Currency</label>
                    <select wire:model="currency" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @foreach (['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'NZD', 'INR', 'PKR', 'AED', 'ZAR'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Default tax rate %</label>
                    <input type="number" step="0.01" wire:model="tax_rate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('tax_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Estimate number prefix</label>
                    <input type="text" wire:model="estimate_prefix" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Invoice number prefix</label>
                    <input type="text" wire:model="invoice_prefix" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Default terms (estimates &amp; invoices)</label>
                <textarea wire:model="default_terms" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">Save settings</button>
        </div>
    </form>
</div>
