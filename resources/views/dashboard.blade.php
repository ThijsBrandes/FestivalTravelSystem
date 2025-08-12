<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Travels') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-4">
                <input
                    type="text"
                    name="search"
                    placeholder="Search bookings..."
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
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-4">
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
                @foreach ($bookings as $booking)
                    <div class="border border-gray-200"></div>
                        <p class="pt-6"><strong>Festival:</strong> {{ $booking->festival->name }}</p>

                        <div>
                            <img
                                src="{{ asset($booking->festival->image ?? 'No image available') }}"
                                alt="{{ $booking->festival->name ?? 'Festival Image' }}"
                                class="w-32 h-48 object-cover mt-2"
                            >
                        </div>

                        <div class="pt-6 text-gray-900">
                            @if ($booking->status === 'pending')
                                <p class="text-red-500">Your booking is pending. Please wait for confirmation.</p>
                            @elseif ($booking->status === 'confirmed')
                                <p><strong>Orderstatus: </strong>{{ $booking->status }}</p>
                            @else
                                <p class="text-red-500">Your booking has been: {{ $booking->status }}</p>
                            @endif
                        </div>

                        <div class="text-gray-900">
                            <p><strong>Quantity:</strong> {{ $booking->ticket_quantity }}</p>
                            <p><strong>Total Price:</strong> €{{ number_format($booking->total_price, 2) }}</p>
                            <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/y H:i') }}</p>
                            <br>
                            <p class="text-sm text-gray-600">Location: {{ $booking->festival->location }}</p>
                            <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($booking->festival->date)->format('d/m/y H:i') }}</p>
                        </div>

                        <div class="pb-6">
                            <button type="button" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    View booking Details
                                </a>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
