<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rumah Pangan Rakyat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('{{ asset('images/pertanian.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="py-16 w-full">
        <div class="flex login-container rounded-lg shadow-2xl overflow-hidden mx-auto max-w-sm lg:max-w-4xl">
            <!-- Photo Section -->
            <div class="hidden lg:block lg:w-1/2 bg-cover bg-center relative"
                style="background-image:url('{{ asset('images/logo_rpr_1.jpg') }}')">
                <!-- Overlay dengan informasi di bagian bawah -->
                <div class="absolute inset-0 flex items-end p-6">
                    <div class="text-white text-center bg-black bg-opacity-40 rounded-2xl p-1 w-full">
                        <h3 class="text-2xl font-bold mb-2">H. Sulaeman L. Hamzah</h3>
                        <p class="text-sm opacity-90 mb-1">Ketua Bidang Pertanian, Peternakan dan Kemandirian Desa</p>
                        <p class="text-sm opacity-90 font-semibold mb-3">DPP Partai NasDem</p>
                    </div>
                </div>
            </div>
            
            <!-- Login Form Section -->
            <div class="w-full p-8 lg:w-1/2">
                <div class="text-center mb-8">
                    <!-- Logo Rumah Pangan Rakyat -->
                    <!-- Logo Rumah Pangan Rakyat -->

                    <h2 class="text-2xl font-semibold text-gray-800">Rumah Pangan Rakyat</h2>
                    
                </div>
                
                <!-- Divider -->
                <div class="mt-6 flex items-center justify-between">
                    <span class="border-b w-1/5 lg:w-1/4"></span>
                    <span class="text-xs text-center text-gray-500 uppercase">Silahkan Login Terlebih Dahulu</span>
                    <span class="border-b w-1/5 lg:w-1/4"></span>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-600 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <!-- Email -->
                    <div class="mt-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email</label>
                        <input 
                            id="email"
                            name="email"
                            type="email" 
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border border-gray-300 rounded-lg py-3 px-4 block w-full appearance-none transition duration-200"
                            placeholder="Masukkan email Anda"
                        />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            @if (Route::has('password.request'))
                                
                            @endif
                        </div>
                        <input 
                            id="password"
                            name="password"
                            type="password" 
                            required
                            autocomplete="current-password"
                            class="bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border border-gray-300 rounded-lg py-3 px-4 block w-full appearance-none transition duration-200"
                            placeholder="Masukkan password Anda"
                        />
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="mt-4 flex items-center">
                        <input 
                            id="remember_me"
                            name="remember"
                            type="checkbox" 
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        >
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Ingat saya
                        </label>
                    </div>
                    
                    <!-- Login Button -->
                    <div class="mt-8">
                        <button 
                            type="submit"
                            class="bg-green-600 text-white font-bold py-3 px-4 w-full rounded-lg hover:bg-green-700 transition duration-200 shadow-md hover:shadow-lg"
                        >
                            Masuk
                        </button>
                    </div>
                </form>
                
                <!-- Sign Up Link -->
                <div class="mt-8  items-center justify-between">
                    <span class="border-b w-1/5 md:w-1/4"></span>
                    @if (Route::has('register'))
                    <a href="https://wa.me/6285714870035?text=Halo%20Admin%2C%20saya%20lupa%20password%20akun%20Rumah%20Pangan%20Rakyat%20NasDem" 
                       target="_blank"
                       class="flex items-center justify-center text-xs text-gray-500 hover:text-green-600 transition duration-200 px-4 py-2">
                        <span class="mr-2">Lupa Password? Hubungi Admin via WhatsApp</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893 0-3.176-1.24-6.165-3.495-8.411"/>
                        </svg>
                    </a> 
                    @endif
                    <span class="border-b w-1/5 md:w-1/4"></span>
                </div>

            </div>

        </div>
        
        <!-- Mobile Photo Info -->
        <div class="lg:hidden mt-6 login-container rounded-lg shadow-2xl p-6 mx-auto max-w-sm">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-green-200 mx-auto mb-4">
                    <img 
                        src="{{ asset('images/anggota-dewan-paksulaeman.jpg') }}" 
                        alt="H. Sulaeman L. Hamzah"
                        class="w-full h-full object-cover"
                    >
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">H. Sulaeman L. Hamzah</h3>
                <p class="text-sm text-gray-600 mb-1">Ketua Bidang Pertanian, Peternakan dan Kemandirian Desa</p>
                <p class="text-sm text-gray-700 font-semibold mb-3">DPP Partai NasDem</p>
                <p class="text-xs text-gray-500">Inisiator Sistem Rumah Pangan Rakyat</p>
            </div>
        </div>
    </div>
</body>
</html>