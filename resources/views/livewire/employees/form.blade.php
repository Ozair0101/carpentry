<div class="mx-auto max-w-2xl">
    <form wire:submit="save" class="space-y-5 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Name *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Role</label>
                <input type="text" wire:model="role" placeholder="Carpenter, finisher…" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
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
                <label class="mb-1 block text-sm font-medium text-stone-700">Salary type</label>
                <select wire:model="salary_type" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach (\App\Models\Employee::SALARY_TYPES as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Salary rate *</label>
                <input type="number" step="0.01" wire:model="salary_rate" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('salary_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-stone-700">Joined on</label>
                <input type="date" wire:model="joined_on" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" wire:model="is_active" class="rounded border-stone-300 text-amber-600"> Active</label>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-stone-700">Notes</label>
            <textarea wire:model="notes" rows="2" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></textarea>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-stone-100 pt-4">
            <a href="{{ route('employees.index') }}" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</a>
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-700">Save employee</button>
        </div>
    </form>
</div>
