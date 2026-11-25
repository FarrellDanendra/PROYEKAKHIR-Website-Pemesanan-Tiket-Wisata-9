<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        <a href="{{ Auth::user()->hasRole('petugas') ? route('petugas.dashboard') : route('dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    @else
                        <a href="{{ route('wisata.index') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </a>
                    @endauth
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (Auth::user()->hasRole('petugas'))
                            <x-nav-link :href="route('petugas.checkin')" :active="request()->routeIs('petugas.checkin')">
                                {{ __('Input Kode Tiket') }}
                            </x-nav-link>
                            <x-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard') || request()->routeIs('petugas.show')">
                                {{ __('Riwayat Tiket') }}
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('pengelola.dashboard') || request()->routeIs('wisatawan.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>

                            <x-nav-link :href="route('wisata.index')" :active="request()->routeIs('wisata.*')">
                                {{ __('Destinasi Wisata') }}
                            </x-nav-link>

                            @hasanyrole('admin|pengelola|wisatawan')
                                <x-nav-link :href="route('pemesanan.index')" :active="request()->routeIs('pemesanan.*')">
                                    {{ __('Pemesanan Tiket') }}
                                </x-nav-link>
                            @endhasanyrole

                            @hasanyrole('admin|pengelola')
                                <x-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                                    {{ __('Kategori') }}
                                </x-nav-link>

                                <x-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                                    {{ __('Laporan Rekap') }}
                                </x-nav-link>
                            @endhasanyrole

                            @role('admin')
                                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    {{ __('Kelola Pengguna') }}
                                </x-nav-link>
                            @endrole
                        @endif
                    @else
                        <x-nav-link :href="route('wisata.index')" :active="request()->routeIs('wisata.*')">
                            {{ __('Destinasi Wisata') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown / Guest Links -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center space-x-2">
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 uppercase">
                                        {{ Auth::user()->roles->pluck('name')->first() ?? Auth::user()->role }}
                                    </span>
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600">
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-sm">
                                {{ __('Register') }}
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if (Auth::user()->hasRole('petugas'))
                    <x-responsive-nav-link :href="route('petugas.checkin')" :active="request()->routeIs('petugas.checkin')">
                        {{ __('Input Kode Tiket') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('petugas.dashboard')" :active="request()->routeIs('petugas.dashboard') || request()->routeIs('petugas.show')">
                        {{ __('Riwayat Tiket') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('wisata.index')" :active="request()->routeIs('wisata.*')">
                        {{ __('Destinasi Wisata') }}
                    </x-responsive-nav-link>

                    @hasanyrole('admin|pengelola|wisatawan')
                        <x-responsive-nav-link :href="route('pemesanan.index')" :active="request()->routeIs('pemesanan.*')">
                            {{ __('Pemesanan Tiket') }}
                        </x-responsive-nav-link>
                    @endhasanyrole

                    @hasanyrole('admin|pengelola')
                        <x-responsive-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                            {{ __('Kategori') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                            {{ __('Laporan Rekap') }}
                        </x-responsive-nav-link>
                    @endhasanyrole

                    @role('admin')
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Kelola Pengguna') }}
                        </x-responsive-nav-link>
                    @endrole
                @endif
            @else
                <x-responsive-nav-link :href="route('wisata.index')" :active="request()->routeIs('wisata.*')">
                    {{ __('Destinasi Wisata') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('Register') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>