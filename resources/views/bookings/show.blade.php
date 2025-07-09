<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking placed!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                    <p><strong>Quantity:</strong> {{ $booking->ticket_quantity }}</p>
                    <p><strong>Total Price:</strong> €{{ number_format($booking->total_price, 2) }}</p>
                    <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
