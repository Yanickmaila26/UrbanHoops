<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Dark mode support for Select2 */
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.5rem;
        }

        .dark .select2-container--default .select2-selection--single {
            background-color: #27272a;
            /* Zinc 800 */
            border-color: #3f3f46;
            /* Zinc 700 */
            color: #fff;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
        }

        .dark .select2-dropdown {
            background-color: #27272a;
            border-color: #3f3f46;
        }

        .dark .select2-results__option {
            color: #fff;
        }

        .dark .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb;
            /* Blue 600 */
        }

        .dark .select2-search__field {
            background-color: #3f3f46;
            color: #fff;
        }
    </style>

    <!-- Additional Styles -->
    <style>
        .sidebar-transition {
            transition: width 0.3s ease;
        }

        .sidebar-collapsed-mode .sidebar-transition {
            width: 4rem;
        }

        .sidebar-collapsed-mode .sidebar-text {
            display: none;
        }

        .sidebar-collapsed-mode .sidebar-group-heading {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-tooltip {
            position: fixed;
            z-index: 9999;
            background-color: rgb(31 41 55);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            white-space: nowrap;
            pointer-events: none;
            transform: translateX(calc(100% + 0.75rem));
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .logo-container {
            padding: 0 1rem;
        }

        .sidebar-logo-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Active link styling */
        .active-nav-link {
            background-color: rgb(59 130 246);
            color: white;
        }

        .active-nav-link:hover {
            background-color: rgb(37 99 235);
        }

        .dark .active-nav-link {
            background-color: rgb(30 64 175);
            color: white;
        }

        .dark .active-nav-link:hover {
            background-color: rgb(29 78 216);
        }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body x-data="{
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    mobileMenuOpen: false,
    darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    },
    toggleMobileMenu() {
        this.mobileMenuOpen = !this.mobileMenuOpen;
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        }
    }
}"
    :class="{ 'sidebar-collapsed-mode': sidebarCollapsed, 'mobile-menu-open': mobileMenuOpen }"
    class="min-h-screen bg-white transition-colors duration-200 dark:bg-zinc-800">

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
        @click="mobileMenuOpen = false"></div>

    <!-- Sidebar (Desktop) -->
    <aside x-show="!mobileMenuOpen || window.innerWidth >= 1024" x-transition
        x-bind:class="{ 'sidebar-collapsed': sidebarCollapsed }"
        class="sidebar-transition fixed inset-y-0 left-0 z-50 hidden w-64 border-r border-zinc-200 bg-zinc-50 lg:block dark:border-zinc-700 dark:bg-zinc-900"
        id="sidebar">
        <div class="flex h-full flex-col">
            <!-- Logo con toggle button -->
            <div class="logo-container border-b border-zinc-200 py-4 dark:border-zinc-700">
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse"
                        wire:navigate>
                        <div
                            class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center text-white font-bold">
                            @if (file_exists(public_path('logo_fondo_claro_admin.png')))
                                <img src="{{ asset('logo.png') }}" alt="Logo" class="h-6 w-6">
                            @else
                                {{ substr(config('app.name'), 0, 2) }}
                            @endif
                        </div>
                        <span
                            class="sidebar-text sidebar-logo-text text-lg font-semibold">{{ config('app.name') }}</span>
                    </a>

                    <!-- Toggle button - solo visible en desktop -->
                    <button @click="toggleSidebar()"
                        class="group hidden h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 transition-all duration-200 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:flex dark:bg-gray-600 dark:hover:bg-gray-500"
                        x-bind:title="sidebarCollapsed ? 'Expandir sidebar' : 'Colapsar sidebar'">
                        <svg class="h-4 w-4 text-gray-600 transition-transform duration-200 dark:text-gray-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            :class="{ 'rotate-180': sidebarCollapsed }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-4">
                <!-- Dashboard -->
                <div class="mb-6">
                    <span
                        class="sidebar-text sidebar-group-heading mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Principal
                    </span>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span class="sidebar-text">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Ventas -->
                <div class="mb-6">
                    <span
                        class="sidebar-text sidebar-group-heading mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Ventas
                    </span>

                    <ul class="space-y-1">
                        <!-- Clientes -->
                        <li>
                            <a href="{{ route('customers.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('customers.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 1.5a6 6 0 00-9-5.197" />
                                </svg>
                                <span class="sidebar-text">Clientes</span>
                            </a>
                        </li>

                        <!-- Facturas -->
                        <li>
                            <a href="{{ route('invoices.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('invoices.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="sidebar-text">Facturas</span>
                            </a>
                        </li>

                        <!-- Carritos de Compra -->
                        <li>
                            <a href="{{ route('shopping-carts.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('shopping-carts.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="sidebar-text">Carritos de Compra</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Compras -->
                <div class="mb-6">
                    <span
                        class="sidebar-text sidebar-group-heading mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Compras
                    </span>

                    <ul class="space-y-1">
                        <!-- Proveedores -->
                        <li>
                            <a href="{{ route('suppliers.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="sidebar-text">Proveedores</span>
                            </a>
                        </li>

                        <!-- Ordenes de Compra -->
                        <li>
                            <a href="{{ route('purchase-orders.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('purchase-orders.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="sidebar-text">Órdenes de Compra</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Inventario -->
                <div class="mb-6">
                    <span
                        class="sidebar-text sidebar-group-heading mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Inventario
                    </span>

                    <ul class="space-y-1">
                        <!-- Productos -->
                        <li>
                            <a href="{{ route('products.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="sidebar-text">Productos</span>
                            </a>
                        </li>

                        <!-- Bodega -->
                        <li>
                            <a href="{{ route('warehouse.index') }}" wire:navigate
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('warehouse.*') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="sidebar-text">Bodega</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- User Info -->
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div
                        class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : 'U' }}
                    </div>
                    <div class="sidebar-text">
                        <p class="text-sm font-medium">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ auth()->check() ? auth()->user()->email : 'email@ejemplo.com' }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <!-- Settings -->
                    {{-- <a href="{{ route('profile.show') }}" wire:navigate
                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        Configuración
                    </a> --}}

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Sidebar -->
    <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 border-r border-zinc-200 bg-zinc-50 lg:hidden dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex h-full flex-col">
            <div class="flex items-center justify-between border-b border-zinc-200 p-4 dark:border-zinc-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse"
                    wire:navigate>
                    <div class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center text-white font-bold">
                        {{ substr(config('app.name'), 0, 2) }}
                    </div>
                    <span class="text-lg font-semibold">{{ config('app.name') }}</span>
                </a>
                <button @click="mobileMenuOpen = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4">
                <!-- Mismo contenido de navegación que el sidebar desktop -->
                <div class="space-y-4">
                    <div>
                        <span
                            class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Principal
                        </span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('dashboard') }}" wire:navigate
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'active-nav-link' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Left side: Mobile menu button -->
                <button @click="mobileMenuOpen = true" class="lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Center: Page title -->
                <div class="flex-1 text-center lg:text-left">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <!-- Right side: User actions -->
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    {{-- <button
                        class="relative text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-red-500"></span>
                    </button> --}}

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-zinc-700">
                        <!-- Sun icon (show when dark) -->
                        <svg x-show="darkMode" class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon (show when light) -->
                        <svg x-show="!darkMode" class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2">
                            <div
                                class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                            </div>
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 rounded-md border border-zinc-200 bg-white py-1 shadow-lg dark:border-zinc-700 dark:bg-zinc-800">
                            {{-- <a href="{{ route('profile.show') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-zinc-700">
                                Perfil
                            </a>
                            <a href="{{ route('profile.show') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-zinc-700">
                                Configuración
                            </a> --}}
                            <div class="border-t border-zinc-200 dark:border-zinc-700"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-zinc-700">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700 dark:bg-green-900/20 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900/20 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div
                    class="mb-4 rounded-lg bg-yellow-50 p-4 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
                    {{ session('warning') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Additional Scripts -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Sidebar collapse persistence
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (sidebarCollapsed) {
                document.body.classList.add('sidebar-collapsed-mode');
            }

            // Close mobile menu on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    Alpine.store('mobileMenuOpen', false);
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
