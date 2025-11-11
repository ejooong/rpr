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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --nasdem-blue: #1e3a8a;
            --nasdem-red: #dc2626;
            --nasdem-light-blue: #3b82f6;
            --nasdem-dark-blue: #1e40af;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
        }
        
        .bg-nasdem-blue { background-color: var(--nasdem-blue); }
        .bg-nasdem-red { background-color: var(--nasdem-red); }
        .bg-nasdem-light-blue { background-color: var(--nasdem-light-blue); }
        .text-nasdem-blue { color: var(--nasdem-blue); }
        .text-nasdem-red { color: var(--nasdem-red); }
        .border-nasdem-blue { border-color: var(--nasdem-blue); }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #1E3A8A 0%, #0A8BCC 50%, #0F3376 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 40;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
        }
        
        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Sidebar Navigation */
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 0.375rem;
            margin: 0.125rem 0.5rem;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 500;
        }
        
        .nav-icon {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .nav-text {
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }
        
        /* Dropdown Styles */
        .nav-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 0.375rem;
            margin: 0.25rem 0.5rem;
        }
        
        .nav-dropdown.open {
            max-height: 500px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
            border-radius: 0.25rem;
            margin: 0.125rem 0;
        }
        
        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .dropdown-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        /* Top Bar */
.top-bar {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    z-index: 9999; /* z-index sangat tinggi */
    position: fixed; /* Ganti dari sticky ke fixed */
    top: 0;
    left: var(--sidebar-width);
    right: 0;
    height: 70px; /* Sesuaikan tinggi */
    transition: left 0.3s ease;
}

.main-content.collapsed .top-bar {
    left: var(--sidebar-collapsed-width);
}

/* Tambahkan padding top ke main content */
main.p-6 {
    padding-top: 90px; /* Sesuaikan dengan tinggi top-bar */
}        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 35;
            }
            
            .mobile-overlay.open {
                display: block;
            }
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

<body class="font-sans antialiased bg-gray-50" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: false,
    activeDropdown: null,
    
    toggleDropdown(dropdown) {
        this.activeDropdown = this.activeDropdown === dropdown ? null : dropdown;
    },
    
    closeSidebar() {
        if (window.innerWidth < 768) {
            this.sidebarOpen = false;
        }
    }
}" :class="{ 'overflow-hidden': sidebarOpen && window.innerWidth < 768 }">

    <!-- Sidebar -->
    <div class="sidebar" :class="{
        'collapsed': sidebarCollapsed,
        'mobile-open': sidebarOpen
    }">
        <!-- Logo -->
        <div class="p-4 border-b border-blue-700 flex items-center justify-between">
            <div class="flex items-center space-x-3" :class="{ 'justify-center': sidebarCollapsed }">
                <div class="bg-white rounded-full p-1 flex-shrink-0">
                    <img src="{{ asset('images/1.png') }}" alt="Logo RPR" class="w-8 h-8 object-contain">
                </div>
                <div x-show="!sidebarCollapsed" class="text-white">
                    <div class="font-bold text-lg">RPR NasDem</div>
                    <div class="text-blue-200 text-xs">Rumah Pangan Rakyat</div>
                </div>
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed" x-show="!sidebarCollapsed" class="text-white hover:text-blue-200">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="py-4 overflow-y-auto h-[calc(100vh-80px)]">
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>

                @auth
                @if(auth()->user()->isAdmin())
                <!-- Wilayah -->
                <div class="nav-item">
                    <div class="nav-link cursor-pointer" 
                         :class="{ 'active': $wire.entangle('activeDropdown') === 'wilayah' }"
                         @click="toggleDropdown('wilayah')">
                        <div class="nav-icon">
                            <i class="fas fa-map"></i>
                        </div>
                        <span class="nav-text">Wilayah</span>
                        <div class="ml-auto" :class="{ 'rotate-180': activeDropdown === 'wilayah' }">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <div class="nav-dropdown" :class="{ 'open': activeDropdown === 'wilayah' }">
                        <a href="{{ route('wilayah.create') }}" 
                           class="dropdown-item {{ request()->routeIs('wilayah.create') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Tambah Wilayah
                        </a>
                        <a href="{{ route('provinsi.index') }}" 
                           class="dropdown-item {{ request()->routeIs('provinsi.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Provinsi
                        </a>
                        <a href="{{ route('kabupaten.index') }}" 
                           class="dropdown-item {{ request()->routeIs('kabupaten.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Kabupaten
                        </a>
                        <a href="{{ route('kecamatan.index') }}" 
                           class="dropdown-item {{ request()->routeIs('kecamatan.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Kecamatan
                        </a>
                        <a href="{{ route('desa.index') }}" 
                           class="dropdown-item {{ request()->routeIs('desa.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Desa
                        </a>
                        <a href="{{ route('wilayah.index') }}" 
                           class="dropdown-item {{ request()->routeIs('wilayah.index') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Semua Wilayah
                        </a>
                    </div>
                </div>

                <!-- Master Data -->
                <div class="nav-item">
                    <div class="nav-link cursor-pointer" 
                         :class="{ 'active': $wire.entangle('activeDropdown') === 'master' }"
                         @click="toggleDropdown('master')">
                        <div class="nav-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <span class="nav-text">Master Data</span>
                        <div class="ml-auto" :class="{ 'rotate-180': activeDropdown === 'master' }">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <div class="nav-dropdown" :class="{ 'open': activeDropdown === 'master' }">
                        <a href="{{ route('sektor.index') }}" 
                           class="dropdown-item {{ request()->routeIs('sektor.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Sektor
                        </a>
                        <a href="{{ route('komoditas.index') }}" 
                           class="dropdown-item {{ request()->routeIs('komoditas.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Data Komoditas
                        </a>
                        <a href="{{ route('poktan.index') }}" 
                           class="dropdown-item {{ request()->routeIs('poktan.*') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Kelompok Tani
                        </a>
                    </div>
                </div>

                <!-- Data BPS -->
                <div class="nav-item">
                    <div class="nav-link cursor-pointer" 
                         :class="{ 'active': $wire.entangle('activeDropdown') === 'bps' }"
                         @click="toggleDropdown('bps')">
                        <div class="nav-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="nav-text">Data BPS</span>
                        <div class="ml-auto" :class="{ 'rotate-180': activeDropdown === 'bps' }">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <div class="nav-dropdown" :class="{ 'open': activeDropdown === 'bps' }">
                        <a href="{{ route('bps-data.dashboard') }}" 
                           class="dropdown-item {{ request()->routeIs('bps-data.dashboard') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Dashboard BPS
                        </a>
                        <a href="{{ route('bps-data.index') }}" 
                           class="dropdown-item {{ request()->routeIs('bps-data.index') ? 'active' : '' }}"
                           @click="closeSidebar()">
                            Kelola Data BPS
                        </a>
                    </div>
                </div>

                <!-- Pengguna -->
                <a href="{{ route('users.index') }}" 
                   class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="nav-text">Pengguna</span>
                </a>
                @endif

                @if(auth()->user()->isPetugas())
                <a href="{{ route('petani.index') }}" 
                   class="nav-link {{ request()->routeIs('petani.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <span class="nav-text">Petani</span>
                </a>
                <a href="{{ route('demplot.index') }}" 
                   class="nav-link {{ request()->routeIs('demplot.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <span class="nav-text">Demplot</span>
                </a>
                <a href="{{ route('produksi.index') }}" 
                   class="nav-link {{ request()->routeIs('produksi.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-industry"></i>
                    </div>
                    <span class="nav-text">Produksi</span>
                </a>
                @endif

                @if(auth()->user()->isDPD())
                <a href="{{ route('laporan.tren') }}" 
                   class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="nav-text">Tren Komoditas</span>
                </a>
                <a href="{{ route('bps-data.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('bps-data.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <span class="nav-text">Data BPS</span>
                </a>
                <a href="{{ route('gis.demplot') }}" 
                   class="nav-link {{ request()->routeIs('gis.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <span class="nav-text">Peta GIS</span>
                </a>
                @endif

                @if(auth()->user()->isPoktan())
                <a href="{{ route('poktan.anggota') }}" 
                   class="nav-link {{ request()->routeIs('poktan.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="nav-text">Anggota</span>
                </a>
                <a href="{{ route('poktan.produksi') }}" 
                   class="nav-link {{ request()->routeIs('produksi.*') ? 'active' : '' }}"
                   @click="closeSidebar()">
                    <div class="nav-icon">
                        <i class="fas fa-industry"></i>
                    </div>
                    <span class="nav-text">Produksi</span>
                </a>
                @endif
                @endauth
            </nav>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false" x-show="sidebarOpen"></div>

    <!-- Main Content -->
    <div class="main-content" :class="{ 'collapsed': sidebarCollapsed }">
        <!-- Top Bar -->
        <div class="top-bar sticky top-0">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Left Section -->
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    @if (isset($header))
                        <div class="text-xl font-semibold text-gray-900">
                            {{ $header }}
                        </div>
                    @endif
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <!-- User Menu -->
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 text-gray-700 hover:text-gray-900">
                            <div class="text-right hidden sm:block">
                                <div class="font-medium">{{ Auth::user()->nama }}</div>
                                <div class="text-sm text-gray-500">{{ Auth::user()->role }}</div>
                            </div>
                            <div class="w-10 h-10 bg-nasdem-blue rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <div class="text-green-800">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <div class="text-red-800">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <div>
                            <div class="text-red-800 font-medium mb-2">Terjadi kesalahan:</div>
                            <ul class="list-disc list-inside text-red-700 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Global SweetAlert2 Configuration -->
    <script>
    // Global SweetAlert2 Functions
    window.SwalModal = {
        deleteConfirm: function(options) {
            const defaults = {
                title: 'Hapus Data?',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            };

            const config = { ...defaults, ...options };
            
            return Swal.fire(config);
        },

        success: function(message, title = 'Berhasil!') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonColor: '#10B981'
            });
        },

        error: function(message, title = 'Error!') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'error',
                confirmButtonColor: '#EF4444'
            });
        }
    };

    // Global Delete Handler
    window.confirmDelete = function(id, itemName) {
        SwalModal.deleteConfirm({
            title: `Hapus ${itemName}?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
        }).then((result) => {
            if (result.isConfirmed) {
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
</body>
</html>