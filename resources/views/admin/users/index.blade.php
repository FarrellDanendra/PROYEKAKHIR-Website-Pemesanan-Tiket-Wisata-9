<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kelola Pengguna (Role & Blokir)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-md">
                    <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-md">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filter Pengguna -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="cari" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Nama / Email</label>
                        <input type="text" name="cari" id="cari" value="{{ request('cari') }}" placeholder="Cari pengguna..."
                               class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="role" class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Role</label>
                        <select name="role" id="role" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Semua Role</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
                            Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-md">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Pengguna -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Akun</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi Blokir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $u)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $u->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($u->id === auth()->id())
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 uppercase">
                                            {{ $u->roles->pluck('name')->first() ?? $u->role }}
                                        </span>
                                    @else
                                        <form action="{{ route('admin.users.update-role', $u) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="text-xs rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                                @php $currentRole = $u->roles->pluck('name')->first() ?? $u->role; @endphp
                                                @foreach ($roles as $r)
                                                    <option value="{{ $r }}" {{ $currentRole === $r ? 'selected' : '' }}>
                                                        {{ ucfirst($r) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded">
                                                Simpan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($u->is_blocked)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Diblokir</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    @if ($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-block', $u) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded text-white {{ $u->is_blocked ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                                                {{ $u->is_blocked ? 'Buka Blokir' : 'Blokir Akun' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Akun Sendiri</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>