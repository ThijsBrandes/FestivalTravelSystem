<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between">
                        <!-- back button -->
                        <div class="mb-4">
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">
                                &larr; Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex items-center space-x-4 mb-4">
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

                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty(request()->search))
                        <div class="mb-4">
                            <p class="text-gray-600">Search results for: <strong>{{ request('search') }}</strong></p>
                        </div>
                        <div class="mb-4">
                            <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex items-center space-x-4">
                                <button type="submit" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                    Clear Search
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($bookings as $booking)
                            <div class="border border-gray-200 p-4 rounded-lg shadow hover:shadow-lg transition">
                                <h3 class="text-lg font-semibold">{{ $booking->festival->name }}</h3>
                                <p class="text-sm text-gray-600">Trip: {{ $booking->trip->starting_location }} -> {{ $booking->trip->destination }}</p>
                                <p class="text-sm text-gray-600">User: {{ $booking->user->name }} - {{ $booking->user->email }}</p>
                                <p class="text-sm text-gray-600">Booked at: {{ \Carbon\Carbon::parse($booking->booked_at)->format('d/m/y H:i') }}</p>
                                <p class="text-sm text-gray-600">Total price: €{{ $booking->total_price }}</p>
                                <p class="text-sm text-gray-600">Amount of tickets: {{ $booking->ticket_quantity }}</p>

                                @if ($booking->status === 'confirmed')
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Confirmed</span>
                                @elseif ($booking->status === 'pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Pending</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Canceled</span>
                                @endif

                                <div class="mt-4">
                                    @if ($booking->status !== 'canceled')
                                        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Cancel booking</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.bookings.reconfirm', $booking) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Are you sure you want to reconfirm this booking? This booking might have been pending before and might not be paid yet.');">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="text-blue-600 hover:underline">Reconfirm booking</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
