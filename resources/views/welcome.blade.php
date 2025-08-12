<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <div class="pt-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-black">Featured Festivals:</h1>
        </div>
    </div>

    <div class="pb-12 pt-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap -mx-4">
                        @foreach ($festivals as $festival)
                            <div class="w-1/2 px-4 mb-4">
                                <h3 class="text-lg font-semibold">{{ $festival->name }}</h3>

                                <img
                                    src="{{ asset($festival->image ?? 'No image available') }}"
                                    alt="{{ $festival->name ?? 'Festival Image' }}"
                                    class="w-full h-48 object-cover mt-2"
                                >

                                <p class="text-sm text-gray-600">Price: €{{ number_format($festival->price, 2) }}</p>
                                <p class="text-sm text-gray-600">Location: {{ $festival->location }}</p>
                                <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($festival->date)->format('d/m/y H:i') }}</p>

                                <button type="button" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    <a href="{{ route('festivals.show', $festival->id) }}" class="text-white">
                                        View Details
                                    </a>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
