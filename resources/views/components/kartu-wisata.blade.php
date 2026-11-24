@props([
    'nama',
    'kategori' => 'Umum',
    'harga' => 0,
    'statusBuka' => true,
    'gambar' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden']) }}>
    <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
        @if ($gambar)
            <img src="{{ asset('storage/' . $gambar) }}" alt="{{ $nama }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif

        <div class="absolute top-3 left-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white shadow-sm">
                {{ $kategori }}
            </span>
        </div>

        <div class="absolute top-3 right-3">
            @if ($statusBuka)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm">
                    Buka
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500 text-white shadow-sm">
                    Tutup
                </span>
            @endif
        </div>
    </div>

    <div class="p-5 flex-1 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900 tracking-tight hover:text-blue-600 transition-colors">
                {{ $nama }}
            </h3>
            
            <p class="mt-1 text-sm font-semibold text-emerald-600">
                Rp {{ number_format($harga, 0, ',', '.') }} <span class="text-xs text-gray-500 font-normal">/ orang</span>
            </p>

            <div class="mt-3 text-sm text-gray-600 leading-relaxed">
                {{ $slot }}
            </div>
        </div>

        @if (isset($footer))
            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>