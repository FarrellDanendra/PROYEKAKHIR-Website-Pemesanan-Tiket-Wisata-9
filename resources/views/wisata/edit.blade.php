<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Destinasi Wisata: ') }} {{ $wisata->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('wisata.update', $wisata) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('wisata._form', ['wisata' => $wisata])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>