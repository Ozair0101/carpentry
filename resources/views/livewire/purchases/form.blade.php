<div class="mx-auto max-w-4xl">
    <form wire:submit="save" class="space-y-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-stone-700">Supplier *</label>
                    <select wire:model="supplier_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">— select supplier —</option>
                        @foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                    @error('supplier_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Bill reference</label>
                    <input type="text" wire:model="reference" placeholder="Supplier invoice #" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Link to job</label>
                    <select wire:model="project_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">— none —</option>
                        @foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Bill date *</label>
                    <input type="date" wire:model="bill_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @error('bill_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-700">Due date</label>
                    <input type="date" wire:model="due_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-stone-800">Items</h3>
            @include('partials.line-items')
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <label class="mb-1 block text-sm font-medium text-stone-700">Notes</label>
            <textarea wire:model="notes" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('bills.index') }}" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</a>
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">Save bill</button>
        </div>
    </form>
</div>
