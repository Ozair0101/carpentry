<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full bg-stone-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'داشبورد' }} · {{ $companyName ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-stone-800 antialiased">
<div x-data="{ sidebarOpen: false }" class="min-h-full">
    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-stone-900/50 lg:hidden" style="display:none"></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 right-0 z-40 w-64 transform bg-stone-900 text-stone-200 transition-transform duration-200 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
        <div class="flex h-16 items-center gap-2 border-b border-stone-700/60 px-5">
            <span class="text-2xl">🪚</span>
            <span class="text-lg font-semibold tracking-tight text-white">{{ \App\Models\Setting::current()->company_name }}</span>
        </div>
        <nav class="flex flex-col gap-1 overflow-y-auto p-3 text-sm">
            @php
                $groups = [
                    'عملیات' => [
                        ['dashboard', 'داشبورد', '▦'],
                        ['customers.index', 'مشتریان', '👤'],
                        ['estimates.index', 'برآوردها', '📝'],
                        ['jobs.index', 'کارها', '🔨'],
                        ['schedule', 'تقویم', '📅'],
                        ['invoices.index', 'فاکتورها', '💵'],
                        ['materials.index', 'مواد', '📦'],
                    ],
                    'امور مالی' => [
                        ['finance', 'نمای مالی', '📊'],
                        ['accounts.index', 'حساب‌ها', '🏦'],
                        ['transactions.index', 'تراکنش‌ها', '💳'],
                        ['bills.index', 'بل‌ها', '🧾'],
                        ['suppliers.index', 'تأمین‌کنندگان', '🚚'],
                        ['employees.index', 'کارمندان', '👷'],
                    ],
                    'گزارش‌ها' => [
                        ['reports.income', 'صورت سود و زیان', '📈'],
                        ['reports.balance', 'بیلانس', '⚖️'],
                        ['reports.monthly', 'گزارش ماهانه', '🗓️'],
                    ],
                    'سیستم' => [
                        ['settings.company', 'تنظیمات', '⚙️'],
                    ],
                ];
            @endphp
            @foreach ($groups as $groupLabel => $items)
                <p class="mt-3 px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-stone-500">{{ $groupLabel }}</p>
                @foreach ($items as [$route, $label, $icon])
                    @php $active = request()->routeIs(\Illuminate\Support\Str::before($route, '.').'*') || request()->routeIs($route); @endphp
                    <a href="{{ route($route) }}" wire:navigate
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition
                              {{ $active ? 'bg-amber-600 text-white' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <span class="w-5 text-center">{{ $icon }}</span>
                        {{ $label }}
                    </a>
                @endforeach
            @endforeach
        </nav>
    </aside>

    {{-- Main --}}
    <div class="lg:pr-64">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-stone-200 bg-white/90 px-4 backdrop-blur lg:px-8">
            <button @click="sidebarOpen = true" class="rounded-lg p-2 text-stone-600 hover:bg-stone-100 lg:hidden">
                <span class="text-xl">☰</span>
            </button>
            <h1 class="text-lg font-semibold text-stone-800">{{ $title ?? 'داشبورد' }}</h1>
            <div class="mr-auto flex items-center gap-4">
                <span class="hidden text-sm text-stone-500 sm:block">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg px-3 py-1.5 text-sm font-medium text-stone-600 hover:bg-stone-100">
                        خروج
                    </button>
                </form>
            </div>
        </header>

        <main class="overflow-x-clip p-4 lg:p-8">
            {{-- Flash toast --}}
            @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    <span>{{ session('status') }}</span>
                    <button @click="show = false" class="text-green-600">✕</button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
