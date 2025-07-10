<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('festivals.index') }}" class="flex items-center space-x-4">
                <input
                    type="text"
                    name="search"
                    placeholder="Search festivals..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>
    </div>

    @if (!empty(request()->search))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <p class="text-gray-600">Search results for: <strong>{{ request('search') }}</strong></p>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <form method="GET" action="{{ route('festivals.index') }}" class="flex items-center space-x-4">
                <button type="submit" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Clear Search
                </button>
            </form>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach ($festivals as $festival)
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">{{ $festival->name }}</h3>

                            <img
                                src="{{ asset($festival->image ?? 'No image available') }}"
                                alt="{{ $festival->name }}"
                                class="w-32 h-48 object-cover mt-2"
                            >

                            <p class="text-sm text-gray-600">Price: €{{ number_format($festival->price, 2) }}</p>
                            <p class="text-sm text-gray-600">Location: {{ $festival->location }}</p>
                            <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($festival->date)->format('d/m/y H:i') }}</p>

                            @auth
                                @if ($festival->availableSeats > 0)
                                    <p class="text-sm text-green-600">Available Seats: {{ $festival->availableSeats }}</p>
                                @else
                                    <p class="text-sm text-red-600">No seats available</p>
                                @endif
                            @endauth

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
</x-app-layout>
