<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    @php
        $user = Auth::user();
        $isWarga = $user && $user->role_id == 3;
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side: Logo & Navigation Links -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-logo class="block h-9 w-auto text-gray-800 dark:text-gray-200" />
                        <span class="ml-2 text-xl font-bold text-gray-800 dark:text-gray-200">NetraTrash</span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-home mr-2"></i> {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if(Auth::user()->isAdmin())
                        <!-- Admin Navigation Links -->
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            <i class="fas fa-tachometer-alt mr-2"></i> {{ __('Admin Panel') }}
                        </x-nav-link>
                    @endif
                    
                    @if(Auth::user()->isPetugas())
                        <!-- Petugas Navigation Links -->
                        <x-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard')">
                            <i class="fas fa-users-cog mr-2"></i> {{ __('Petugas Panel') }}
                        </x-nav-link>
                    @endif
                    
                    @if($isWarga)
                        <!-- Warga Navigation Links -->
                        <x-nav-link :href="route('warga.dashboard')" :active="request()->routeIs('warga.dashboard')">
                            <i class="fas fa-user mr-2"></i> {{ __('Warga Panel') }}
                        </x-nav-link>
                        
                        <!-- Tukar Poin (Barang) Menu -->
                        <x-nav-link :href="route('warga.barang.index')" :active="request()->routeIs('warga.barang.*')">
                            <i class="fas fa-shopping-bag mr-2"></i> {{ __('Tukar Poin') }}
                        </x-nav-link>
                        
                        <!-- Simple test untuk memastikan -->
                        <x-nav-link href="#" class="text-green-600">
                            <i class="fas fa-check mr-2"></i> Warga Terdeteksi
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side: User Profile Dropdown & Notifications -->
            <div class="flex items-center">
                <!-- Notifications Bell (Optional) -->
                @if(Auth::user()->isWarga() || Auth::user()->isPetugas())
                <div class="relative mr-4">
                    <a href="{{ route('notifikasi.index') }}" class="relative text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i class="fas fa-bell text-xl"></i>
                        @php
                            $unreadCount = \App\Models\Notifikasi::where('user_id', Auth::id())
                                ->where('dibaca', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>
                </div>
                @endif

                <!-- User Profile Dropdown -->
                <div class="relative ml-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-netra-500">
                                <!-- Avatar/Profile Picture -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-netra-500 flex items-center justify-center text-white font-bold">
                                        @if(Auth::user()->photo)
                                            <img src="{{ Storage::url(Auth::user()->photo) }}" 
                                                 class="h-8 w-8 rounded-full object-cover" 
                                                 alt="{{ Auth::user()->name }}">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="ml-3 hidden md:block text-left">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ Auth::user()->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @if(Auth::user()->isAdmin())
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-800 text-xs rounded-full">Admin</span>
                                            @elseif(Auth::user()->isPetugas())
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">Petugas</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">Warga</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Header with user info -->
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ Auth::user()->email }}
                                </div>
                                @if($isWarga)
                                <div class="mt-1 text-xs">
                                    <span class="text-netra-600 dark:text-netra-400 font-semibold">
                                        {{ number_format(Auth::user()->total_points, 0, ',', '.') }} Poin
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- Dropdown Items -->
                            <div class="py-1">
                                <!-- Profile Link -->
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                                    <span>{{ __('Profile') }}</span>
                                </x-dropdown-link>

                                <!-- Dashboard based on role -->
                                @if(Auth::user()->isAdmin())
                                    <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center">
                                        <i class="fas fa-tachometer-alt mr-2 text-gray-400"></i>
                                        <span>{{ __('Admin Dashboard') }}</span>
                                    </x-dropdown-link>
                                @elseif(Auth::user()->isPetugas())
                                    <x-dropdown-link :href="route('petugas.dashboard')" class="flex items-center">
                                        <i class="fas fa-users-cog mr-2 text-gray-400"></i>
                                        <span>{{ __('Petugas Dashboard') }}</span>
                                    </x-dropdown-link>
                                @else
                                    <x-dropdown-link :href="route('warga.dashboard')" class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        <span>{{ __('Warga Dashboard') }}</span>
                                    </x-dropdown-link>
                                @endif

                                <!-- Divider -->
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" 
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        <span>{{ __('Log Out') }}</span>
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger menu for mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-home mr-2"></i> {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <i class="fas fa-tachometer-alt mr-2"></i> {{ __('Admin Panel') }}
                </x-responsive-nav-link>
            @endif
            
            @if(Auth::user()->isPetugas())
                <x-responsive-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard')">
                    <i class="fas fa-users-cog mr-2"></i> {{ __('Petugas Panel') }}
                </x-responsive-nav-link>
            @endif
             
            @if($isWarga)
                <x-responsive-nav-link :href="route('warga.dashboard')" :active="request()->routeIs('warga.dashboard')">
                    <i class="fas fa-user mr-2"></i> {{ __('Warga Panel') }}
                </x-responsive-nav-link>
                
                <!-- Tukar Poin (Barang) Menu -->
                <x-responsive-nav-link :href="route('warga.barang.index')" :active="request()->routeIs('warga.barang.*')">
                    <i class="fas fa-shopping-bag mr-2"></i> {{ __('Tukar Poin') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive User Menu -->
        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-netra-500 flex items-center justify-center text-white font-bold">
                    @if(Auth::user()->photo)
                        <img src="{{ Storage::url(Auth::user()->photo) }}" 
                             class="h-10 w-10 rounded-full object-cover" 
                             alt="{{ Auth::user()->name }}">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800 dark:text-gray-200">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ Auth::user()->email }}
                    </div>
                    @if($isWarga)
                    <div class="text-xs text-netra-600 dark:text-netra-400 font-semibold mt-1">
                        {{ number_format(Auth::user()->total_points, 0, ',', '.') }} Poin
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user-circle mr-2"></i> {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Role-specific dashboard -->
                @if(Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i> {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                @elseif(Auth::user()->isPetugas())
                    <x-responsive-nav-link :href="route('petugas.dashboard')">
                        <i class="fas fa-users-cog mr-2"></i> {{ __('Petugas Dashboard') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('warga.dashboard')">
                        <i class="fas fa-user mr-2"></i> {{ __('Warga Dashboard') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Notifications for mobile -->
                @if(Auth::user()->isWarga() || Auth::user()->isPetugas())
                    <x-responsive-nav-link :href="route('notifikasi.index')">
                        <i class="fas fa-bell mr-2"></i> {{ __('Notifications') }}
                        @php
                            $unreadCount = \App\Models\Notifikasi::where('user_id', Auth::id())
                                ->where('dibaca', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </x-responsive-nav-link>
                @endif

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-600 hover:text-red-800 dark:text-red-400">
                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
    .text-netra-500 {
        color: #10b981; /* Warna hijau NetraTrash */
    }
    .bg-netra-500 {
        background-color: #10b981;
    }
    .focus-ring-netra-500:focus {
        ring-color: #10b981;
    }
</style>
@endpush