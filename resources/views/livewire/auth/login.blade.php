<div>
    <div class="mb-6 text-center">
        <div class="mb-2 text-4xl">🪚</div>
        <h1 class="text-2xl font-bold text-stone-800">{{ \App\Models\Setting::current()->company_name }}</h1>
        <p class="text-sm text-stone-500">Sign in to manage your workshop</p>
    </div>

    <form wire:submit="login" class="space-y-4 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div>
            <label class="mb-1 block text-sm font-medium text-stone-700">Email</label>
            <input type="email" wire:model="email" autofocus
                   class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-stone-700">Password</label>
            <input type="password" wire:model="password"
                   class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-stone-600">
            <input type="checkbox" wire:model="remember" class="rounded border-stone-300 text-amber-600 focus:ring-amber-500">
            Remember me
        </label>

        <button type="submit"
                class="w-full rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
            <span wire:loading.remove wire:target="login">Sign in</span>
            <span wire:loading wire:target="login">Signing in…</span>
        </button>
    </form>
</div>
