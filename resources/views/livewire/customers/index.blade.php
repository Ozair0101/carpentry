<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative sm:w-80">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search customers…"
                   class="w-full rounded-lg border border-stone-300 py-2 pl-9 pr-3 text-sm focus:border-amber-500 focus:ring-amber-500">
            <span class="pointer-events-none absolute left-3 top-2.5 text-stone-400">🔍</span>
        </div>
        <a href="{{ route('customers.create') }}" wire:navigate
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
            + New customer
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Contact</th>
                    <th class="hidden px-4 py-3 md:table-cell">Jobs</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($customers as $customer)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('customers.show', $customer) }}" wire:navigate class="font-medium text-stone-800 hover:text-amber-700">
                                {{ $customer->name }}
                            </a>
                            @if ($customer->company)<p class="text-xs text-stone-500">{{ $customer->company }}</p>@endif
                        </td>
                        <td class="hidden px-4 py-3 text-stone-600 sm:table-cell">
                            <div>{{ $customer->phone }}</div>
                            <div class="text-xs text-stone-400">{{ $customer->email }}</div>
                        </td>
                        <td class="hidden px-4 py-3 text-stone-600 md:table-cell">{{ $customer->projects_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('customers.edit', $customer) }}" wire:navigate class="text-sm font-medium text-stone-500 hover:text-amber-700">Edit</a>
                            <button wire:click="confirmDelete({{ $customer->id }})" class="ml-3 text-sm font-medium text-stone-400 hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-stone-400">No customers yet. Add your first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $customers->links() }}</div>

    {{-- Delete confirmation --}}
    @if ($deletingId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-stone-800">Delete customer?</h3>
                <p class="mt-2 text-sm text-stone-500">This also deletes their estimates, jobs and invoices. This cannot be undone.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('deletingId', null)" class="rounded-lg px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100">Cancel</button>
                    <button wire:click="delete" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
