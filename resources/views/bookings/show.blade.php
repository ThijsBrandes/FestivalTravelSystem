<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking placed!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div>
                    <img
                        src="{{ asset($booking->festival->image ?? 'No image available') }}"
                        alt="{{ $booking->festival->name }}"
                        class="w-full h-[300px] object-cover mt-2 rounded-lg"
                    >
                </div>

                <div class="p-6 text-gray-900">
                    @if ($booking->status === 'pending')
                        <p class="text-red-500">Your booking is pending. Please wait for confirmation.</p>
                    @elseif ($booking->status === 'confirmed')
                        <p>Your order has been: {{ $booking->status }}</p>
                    @else
                        <p class="text-red-500">Your booking has been: {{ $booking->status }}</p>
                    @endif
                </div>

                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">Booking Details</h3>
                    <p><strong>Festival:</strong> {{ $booking->festival->name }}</p>
                    <p class="mt-2 text-sm text-gray-600">Price per ticket: €{{ number_format($booking->festival->price, 2) }}</p>
                    <p><strong>Quantity:</strong> {{ $booking->ticket_quantity }}</p>
                    <p><strong>Total Price:</strong> €{{ number_format($booking->total_price, 2) }}</p>
                    <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/y H:i') }}</p>
                    <br>
                    <p class="text-sm text-gray-600">Location: {{ $booking->festival->location }}</p>
                    <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($booking->festival->date)->format('d/m/y H:i') }}</p>
                    <p class="mt-2 text-sm text-gray-600">Booked by: {{ $booking->user->name }}</p>
                    <p class="mt-2 text-sm text-gray-600">Email: {{ $booking->user->email }}</p>
                    <br>
                    <p class="text-sm text-gray-600">Your booking ID is: <strong>{{ $booking->id }}</strong></p>

                </div>

                <div class="p-6">
                    <button type="button" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <a href="{{ route('festivals.show', $booking->festival->id) }}" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            View Festival Details
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
