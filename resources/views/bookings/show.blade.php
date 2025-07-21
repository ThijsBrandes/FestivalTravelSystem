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
                    <h1 class="text-2xl font-black">Booking Details</h1>
                    <p class="text-sm text-gray-600">Your booking ID is: <strong>{{ $booking->id }}</strong></p>
                    <h2 class="text-lg font-bold">Festival Details</h2>
                    <p><strong>Festival:</strong> {{ $booking->festival->name }}</p>
                    <p><strong>Location:</strong> {{ $booking->festival->location }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->festival->date)->format('d/m/y H:i') }}</p>
                    <br>
                    <h2 class="text-lg font-bold">Ticket Details</h2>
                    <p><strong>Price per ticket:</strong> €{{ number_format($booking->festival->price, 2) }}</p>
                    <p><strong>Quantity:</strong> {{ $booking->ticket_quantity }}</p>
                    @if ($booking->reward)
                        <p><strong>Reward used:</strong> {{ $booking->reward->name }} ({{ $booking->reward->discount_percentage }}% discount)</p>
                    @else
                        <p><strong>No reward used.</strong></p>
                    @endif
                    <p><strong>Total Price:</strong> €{{ number_format($booking->total_price, 2) }}</p>
                    <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/y H:i') }}</p>
                    <br>
                    <h2 class="text-lg font-bold">User Details</h2>
                    <p><strong>Booked by:</strong> {{ $booking->user->name }}</p>
                    <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                    <p><strong>Reward points received:</strong> {{ $booking->total_points }}</p>
                    <br>
                    <h2 class="text-lg font-bold">Trip Information</h2>
                    @if ($booking->trip)
                        <p><strong>Bus:</strong> {{ $booking->trip->bus->name }}</p>
                        <p><strong>License Plate:</strong> {{ $booking->trip->bus->license_plate }}</p>
                        <p><strong>Bus status:</strong> {{ $booking->trip->bus->status }}</p>
                        <p><strong>Departure time:</strong> {{ \Carbon\Carbon::parse($booking->trip->departure_time)->format('d/m/y H:i') }} <strong>At:</strong> {{ $booking->trip->starting_location }}</p>
                        <p><strong>Arrival time:</strong> {{ \Carbon\Carbon::parse($booking->trip->arrival_time)->format('d/m/y H:i') }} <strong>At:</strong> {{ $booking->trip->destination }}</p>
                        <p><strong>Total Seats:</strong> {{ $booking->trip->bus->total_seats }}</p>
                        <p><strong>Available Seats:</strong> {{ $booking->trip->bus->available_seats }}</p>
                    @else
                        <p class="text-sm text-red-600">No trip information available for this booking.</p>
                    @endif
                </div>

                <div class="flex w-full justify-between">
                    <div class="p-6">
                        <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            <a href="{{ route('festivals.show', $booking->festival->id) }}" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                View Festival Details
                            </a>
                        </button>
                    </div>

                    @if ($booking->status !== 'canceled')
                        <div class="p-6">
                            <form method="POST" action="{{ route('booking.update', $booking) }}" class="" onsubmit="return confirm('Are you sure you want to cancel your booking? This action can not be undone. Your points will also be taken from your account.');">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Cancel booking
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
