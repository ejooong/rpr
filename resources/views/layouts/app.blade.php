<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - RPR NasDem</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Additional Styles -->
<style>
    :root {
        --nasdem-blue: #1e3a8a;
        --nasdem-red: #dc2626;
        --nasdem-light-blue: #3b82f6;
        --nasdem-dark-blue: #1e40af;
    }
    
    .bg-nasdem-blue { background-color: var(--nasdem-blue); }
    .bg-nasdem-red { background-color: var(--nasdem-red); }
    .bg-nasdem-light-blue { background-color: var(--nasdem-light-blue); }
    .text-nasdem-blue { color: var(--nasdem-blue); }
    .text-nasdem-red { color: var(--nasdem-red); }
    .border-nasdem-blue { border-color: var(--nasdem-blue); }
    
    /* Navbar dan Footer dengan gradient yang sama persis */
    .gradient-nasdem,
    .gradient-footer {
        background: linear-gradient(135deg, #1E3A8A 0%, #0A8BCC 50%, #0F3376 100%);
        background-size: 200% 200%;
        animation: gradient-shift 8s ease infinite;
    }
    
    @keyframes gradient-shift {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
    


    .btn-nasdem {
        @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200;
    }
    
    .btn-nasdem-red {
        @apply bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200;
    }

    [x-cloak] {
    display: none !important;
    }
</style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-size: 0.875rem !important;
        }
    </style>
    @stack('styles')
    
</head>
<body class="font-sans antialiased bg-gray-50 ">

     <!-- Background dengan logo NasDem subtle -->
    <div class="fixed inset-0 -z-10 bg-gray-50">
        <!-- Pattern Background -->
        <div class="absolute inset-0 bg-[url('{{ asset('images/nasdem_bg.png') }}')] bg-center bg-no-repeat opacity-5 bg-[length:200px_200px]"></div>
        
        <!-- Animated Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/40 via-transparent to-green-50/40 animate-pulse-slow"></div>
    </div>

    <!-- Navigation -->
    <nav class="gradient-nasdem shadow-lg "x-data="{ 
    mobileMenuOpen: false,
    wilayahMobileOpen: false,
    masterDataMobileOpen: false,
    bpsMobileOpen: false 
}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
<!-- Logo -->
<div class="flex items-center">
    <div class="flex-shrink-0 flex items-center">
        <div class="bg-white rounded-full p-1 mr-3">
            <img 
                src="{{ asset('images/logo-rpr.png') }}" 
                alt="Logo RPR" 
                class="w-8 h-8 object-contain"
            >
        </div>
        <div>
            <h1 class="text-white text-xl font-bold">RPR NasDem</h1>
            <p class="text-blue-200 text-sm">Rumah Pangan Rakyat</p>
        </div>
    </div>
</div>


<!-- Navigation Links -->
<div class="hidden sm:flex sm:items-center sm:space-x-4">   
    @auth
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-white hover:text-blue-200">
            Dashboard
        </x-nav-link>


@if(auth()->user()->isAdmin())

    <!-- Dropdown Wilayah -->
    <div class="relative group" x-data="{ open: false }">
        <button @click="open = !open" 
                class="flex items-center text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out"
                :class="{ 'bg-blue-700': request()->routeIs(['provinsi.*', 'kabupaten.*', 'kecamatan.*', 'desa.*', 'wilayah.*']) }">
            <span>Wilayah</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out-back duration-400"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in-back duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
            <div class="py-1" role="menu" aria-orientation="vertical">
                <!-- Quick Add -->
                <a href="{{ route('wilayah.create') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('wilayah.create') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Wilayah
                </a>
                
                <div class="border-t border-gray-100 my-1"></div>

                <!-- Provinsi -->
                <a href="{{ route('provinsi.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('provinsi.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Data Provinsi
                </a>

                <!-- Kabupaten -->
                <a href="{{ route('kabupaten.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('kabupaten.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Data Kabupaten/Kota
                </a>

                <!-- Kecamatan -->
                <a href="{{ route('kecamatan.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('kecamatan.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Data Kecamatan
                </a>

                <!-- Desa -->
                <a href="{{ route('desa.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('desa.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Data Desa/Kelurahan
                </a>

                <div class="border-t border-gray-100 my-1"></div>

                <!-- All Wilayah -->
                <a href="{{ route('wilayah.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('wilayah.index') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Semua Wilayah
                </a>
            </div>
        </div>
    </div>

    <!-- Dropdown Master Data -->
    <div class="relative group" x-data="{ open: false }">
        <button @click="open = !open" 
                class="flex items-center text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out"
                :class="{ 'bg-blue-700': request()->routeIs(['sektor.*', 'komoditas.*', 'poktan.*']) }">
            <span>Master Data</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
            <div class="py-1" role="menu" aria-orientation="vertical">
                <!-- Sektor -->
                <a href="{{ route('sektor.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('sektor.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    Data Sektor
                </a>

                <!-- Komoditas -->
                <a href="{{ route('komoditas.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('komoditas.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                    Data Komoditas
                </a>

                <div class="border-t border-gray-100 my-1"></div>

                <!-- Poktan -->
                <a href="{{ route('poktan.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('poktan.*') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Kelompok Tani
                </a>
            </div>
        </div>
    </div>

    <!-- Dropdown Data BPS -->
    <div class="relative group" x-data="{ open: false }">
        <button @click="open = !open" 
                class="flex items-center text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out"
                :class="{ 'bg-blue-700': request()->routeIs('bps-data.*') }">
            <span>Data BPS</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
            <div class="py-1" role="menu" aria-orientation="vertical">
                <!-- Dashboard BPS -->
                <a href="{{ route('bps-data.dashboard') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('bps-data.dashboard') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard BPS
                </a>

                <!-- Kelola Data BPS -->
                <a href="{{ route('bps-data.index') }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 group"
                   :class="{ 'bg-blue-50 text-blue-600': request()->routeIs('bps-data.index') }">
                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                    Kelola Data BPS
                </a>
            </div>
        </div>
    </div>

    <!-- Pengguna -->
    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" class="text-white hover:text-blue-200">
        Pengguna
    </x-nav-link>
@endif

                        


                        @if(auth()->user()->isPetugas())
                            <x-nav-link href="{{ route('petani.index') }}" :active="request()->routeIs('petani.*')" class="text-white hover:text-blue-200">
                                Petani
                            </x-nav-link>
                            <x-nav-link href="{{ route('demplot.index') }}" :active="request()->routeIs('demplot.*')" class="text-white hover:text-blue-200">
                                Demplot
                            </x-nav-link>
                            <x-nav-link href="{{ route('produksi.index') }}" :active="request()->routeIs('produksi.*')" class="text-white hover:text-blue-200">
                                Produksi
                            </x-nav-link>
                        @endif
                        
                        @if(auth()->user()->isDPD())
                            <x-nav-link href="{{ route('laporan.tren') }}" :active="request()->routeIs('laporan.*')" class="text-white hover:text-blue-200">
                                Tren Komoditas
                            </x-nav-link>
                            <x-nav-link href="{{ route('bps-data.dashboard') }}" :active="request()->routeIs('bps-data.*')" class="text-white hover:text-blue-200">
                                Data BPS
                            </x-nav-link>

                            <x-nav-link href="{{ route('gis.demplot') }}" :active="request()->routeIs('gis.*')" class="text-white hover:text-blue-200">
                                Peta GIS
                            </x-nav-link>
                        @endif
                        
                        @if(auth()->user()->isPoktan())
                            <x-nav-link href="{{ route('poktan.anggota') }}" :active="request()->routeIs('poktan.*')" class="text-white hover:text-blue-200">
                                Anggota
                            </x-nav-link>
                            <x-nav-link href="{{ route('poktan.produksi') }}" :active="request()->routeIs('produksi.*')" class="text-white hover:text-blue-200">
                                Produksi
                            </x-nav-link>
                        @endif
                    @endauth
                </div>

                <!-- Settings Dropdown -->
                @auth
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-white hover:text-blue-200 transition duration-150 ease-in-out">
                                    <div>{{ Auth::user()->nama }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('profile.edit') }}">
                                    Profile
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        Log Out
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth

<!-- Hamburger -->
<div class="-mr-2 flex items-center sm:hidden">
    <button @click="mobileMenuOpen = !mobileMenuOpen" 
            class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-blue-200 hover:bg-blue-700 focus:outline-none focus:bg-blue-700 focus:text-white transition-all duration-300 ease-in-out transform hover:scale-105">
        <svg class="h-6 w-6 transition-transform duration-300" 
             :class="{'rotate-90': mobileMenuOpen}" 
             stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen}" 
                  class="inline-flex transition-opacity duration-300" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen}" 
                  class="hidden transition-opacity duration-300" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
</div>

<!-- Responsive Navigation Menu -->
<div x-show="mobileMenuOpen" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-x-full"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-250"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-full"
     class="sm:hidden bg-blue-800 border-t border-blue-700 absolute top-16 left-0 right-0 z-50 shadow-xl">
    <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-white hover:bg-blue-700 transition-colors duration-200">
            Dashboard
        </x-responsive-nav-link>
        
        <!-- Mobile menu items based on role -->
        @auth
            @if(auth()->user()->isAdmin())
                <!-- Wilayah Submenu -->
                <div class="px-4 py-2">
                    <button @click="wilayahMobileOpen = !wilayahMobileOpen" 
                            class="flex items-center justify-between w-full text-white hover:text-blue-200 text-left transition-colors duration-200">
                        <span>Wilayah</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" 
                             :class="{'rotate-180': wilayahMobileOpen}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="wilayahMobileOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="mt-2 ml-4 space-y-2 border-l border-blue-600 pl-4 overflow-hidden">
                        <x-responsive-nav-link href="{{ route('wilayah.create') }}" :active="request()->routeIs('wilayah.create')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Tambah Wilayah
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('provinsi.index') }}" :active="request()->routeIs('provinsi.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Provinsi
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('kabupaten.index') }}" :active="request()->routeIs('kabupaten.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Kabupaten
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('kecamatan.index') }}" :active="request()->routeIs('kecamatan.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Kecamatan
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('desa.index') }}" :active="request()->routeIs('desa.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Desa
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('wilayah.index') }}" :active="request()->routeIs('wilayah.index')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Semua Wilayah
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Master Data Submenu -->
                <div class="px-4 py-2">
                    <button @click="masterDataMobileOpen = !masterDataMobileOpen" 
                            class="flex items-center justify-between w-full text-white hover:text-blue-200 text-left transition-colors duration-200">
                        <span>Master Data</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" 
                             :class="{'rotate-180': masterDataMobileOpen}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="masterDataMobileOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="mt-2 ml-4 space-y-2 border-l border-blue-600 pl-4 overflow-hidden">
                        <x-responsive-nav-link href="{{ route('sektor.index') }}" :active="request()->routeIs('sektor.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Sektor
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('komoditas.index') }}" :active="request()->routeIs('komoditas.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Data Komoditas
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('poktan.index') }}" :active="request()->routeIs('poktan.*')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Kelompok Tani
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Data BPS Submenu -->
                <div class="px-4 py-2">
                    <button @click="bpsMobileOpen = !bpsMobileOpen" 
                            class="flex items-center justify-between w-full text-white hover:text-blue-200 text-left transition-colors duration-200">
                        <span>Data BPS</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" 
                             :class="{'rotate-180': bpsMobileOpen}" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="bpsMobileOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 max-h-96"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="mt-2 ml-4 space-y-2 border-l border-blue-600 pl-4 overflow-hidden">
                        <x-responsive-nav-link href="{{ route('bps-data.dashboard') }}" :active="request()->routeIs('bps-data.dashboard')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Dashboard BPS
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('bps-data.index') }}" :active="request()->routeIs('bps-data.index')" class="text-blue-200 hover:bg-blue-700 transition-colors duration-200">
                            Kelola Data BPS
                        </x-responsive-nav-link>
                    </div>
                </div>

                <x-responsive-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Pengguna
                </x-responsive-nav-link>
            @endif
            
            @if(auth()->user()->isPetugas())
                <x-responsive-nav-link href="{{ route('petani.index') }}" :active="request()->routeIs('petani.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Petani
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('demplot.index') }}" :active="request()->routeIs('demplot.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Demplot
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('produksi.index') }}" :active="request()->routeIs('produksi.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Produksi
                </x-responsive-nav-link>
            @endif
            
            @if(auth()->user()->isDPD())
                <x-responsive-nav-link href="{{ route('laporan.tren') }}" :active="request()->routeIs('laporan.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Tren Komoditas
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('bps-data.dashboard') }}" :active="request()->routeIs('bps-data.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Data BPS
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('gis.demplot') }}" :active="request()->routeIs('gis.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Peta GIS
                </x-responsive-nav-link>
            @endif
            
            @if(auth()->user()->isPoktan())
                <x-responsive-nav-link href="{{ route('poktan.anggota') }}" :active="request()->routeIs('poktan.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Anggota
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('poktan.produksi') }}" :active="request()->routeIs('produksi.*')" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Produksi
                </x-responsive-nav-link>
            @endif
        @endauth
    </div>

    <!-- Responsive Settings Options -->
    @auth
        <div class="pt-4 pb-1 border-t border-blue-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->nama }}</div>
                <div class="font-medium text-sm text-blue-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.edit') }}" class="text-white hover:bg-blue-700 transition-colors duration-200">
                    Profile
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-white hover:bg-blue-700 transition-colors duration-200">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    @endauth
</div>




    </nav>

    <!-- Page Content -->
    <main class="py-6">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- New Beautiful Footer dengan Theme Pertanian -->
    <footer class="gradient-footer text-white py-12 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10">
                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L4 6v2c0 6.627 3.582 12 8 12s8-5.373 8-12V6l-8-4z"/>
                </svg>
            </div>
            <div class="absolute top-20 right-20">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L4 6v2c0 6.627 3.582 12 8 12s8-5.373 8-12V6l-8-4z"/>
                </svg>
            </div>
            <div class="absolute bottom-20 left-20">
                <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L4 6v2c0 6.627 3.582 12 8 12s8-5.373 8-12V6l-8-4z"/>
                </svg>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo & Description -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="bg-white rounded-full p-2 mr-3">
                            <img 
                                src="{{ asset('images/logo-rpr.png') }}" 
                                alt="Logo RPR" 
                                class="w-8 h-8 object-contain"
                            >
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">RPR NasDem</h3>
                            <p class="text-green-200 text-sm">Rumah Pangan Rakyat</p>
                        </div>
                    </div>
                    <p class="text-green-100 mb-4 text-lg">
                        🌱 Membangun Ketahanan Pangan Nasional melalui Inovasi dan Teknologi Pertanian
                    </p>
                    <div class="flex space-x-4">
                        <div class="flex items-center text-green-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                            <span>Seluruh Indonesia</span>
                        </div>
                    </div>
                </div>

                <!-- Kontak -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        Kontak Kami
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-green-100">
                            <svg class="w-5 h-5 mr-3 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <span>info@rpr-nasdem.id</span>
                        </div>
                        <div class="flex items-center text-green-100">
                            <svg class="w-5 h-5 mr-3 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <span>(021) 1234-5678</span>
                        </div>
                    </div>
                </div>

                <!-- Partai NasDem -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9L3 7V9L9 15V21H15V15L21 9Z"/>
                        </svg>
                        Partai NasDem
                    </h3>
                    <p class="text-green-100 mb-4">
                        Partai Nasional Demokrat - Indonesia Bangkit, Indonesia Tangguh
                    </p>
                    <div class="flex space-x-3">
                        <div class="bg-white/20 rounded-lg p-2 text-center">
                            <div class="text-yellow-300 font-bold">🌾</div>
                            <div class="text-xs text-white">Pertanian</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-2 text-center">
                            <div class="text-green-300 font-bold">🚜</div>
                            <div class="text-xs text-white">Teknologi</div>
                        </div>
                        <div class="bg-white/20 rounded-lg p-2 text-center">
                            <div class="text-blue-300 font-bold">🤝</div>
                            <div class="text-xs text-white">Kemitraan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-green-600/30 mt-8 pt-6 text-center">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-green-200 text-sm mb-4 md:mb-0">
                        &copy; {{ date('Y') }} RPR NasDem. All rights reserved.
                    </p>
                    <div class="flex space-x-4 text-green-200">
                        <span>🌱 Pertanian Maju</span>
                        <span>•</span>
                        <span>🇮🇩 Indonesia Sejahtera</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')


    
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Global SweetAlert2 Configuration -->
<script>
// Global SweetAlert2 Functions
window.SwalModal = {
    // Delete Confirmation
    deleteConfirm: function(options) {
        const defaults = {
            title: 'Hapus Data?',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 text-sm font-medium rounded-md',
                cancelButton: 'px-4 py-2 text-sm font-medium rounded-md'
            },
            buttonsStyling: false
        };

        const config = { ...defaults, ...options };
        
        return Swal.fire(config);
    },

    // Success Message
    success: function(message, title = 'Berhasil!') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
            },
            buttonsStyling: false
        });
    },

    // Error Message
    error: function(message, title = 'Error!') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
            },
            buttonsStyling: false
        });
    },

    // Loading
    loading: function(title = 'Memproses...') {
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
};

// Global Delete Handler
window.confirmDelete = function(id, itemName, details = '') {
    const htmlContent = details ? `
        <div class="text-left">
            <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?</p>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-3">
                <div class="flex items-center space-x-2 mb-1">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-red-800">Detail Data:</span>
                </div>
                <div class="text-sm text-gray-700">
                    ${details}
                </div>
            </div>
            <p class="text-red-600 font-medium mt-3 text-sm">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
    ` : `
        <div class="text-left">
            <p class="mb-3">Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?</p>
            <p class="text-red-600 font-medium text-sm">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
    `;

    SwalModal.deleteConfirm({
        title: `Hapus ${itemName}?`,
        html: htmlContent
    }).then((result) => {
        if (result.isConfirmed) {
            SwalModal.loading('Menghapus...');
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
};

// Handle flash messages globally
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        SwalModal.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        SwalModal.error('{{ session('error') }}');
    @endif
});
</script>

@stack('scripts')


<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error') && session('auto_modal'))
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorMessage').textContent = "{{ session('error') }}";
        errorModal.show();
    @endif
});
</script>



</body>
</html>