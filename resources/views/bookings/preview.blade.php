<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Preview Order
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-3xl font-bold mb-3">{{ $festival->name }}</h2>
                    <p class="text-sm text-gray-600">Price per ticket: €{{ number_format($festival->price, 2) }}</p>
                    <p class="text-sm text-gray-600">Amount of tickets: {{ $quantity }}</p>
                    <p class="text-sm text-gray-600">Total price before possible discount: €{{ number_format($totalPrice, 2) }}</p>
                    <p class="text-sm text-gray-600">Points to receive: {{ $totalPoints }}</p>
                    <p class="text-sm text-gray-600">Festival location: {{ $festival->location }}</p>
                    <p class="text-sm text-gray-600">Festival date: {{ \Carbon\Carbon::parse($festival->date)->format('d/m/y H:i') }}</p>

                    <!-- trip information -->
                    @if (!empty($trip))
                        <h3 class="text-lg font-semibold mt-4">Trip Information</h3>
                        <ul class="pl-5">
                                <li>
                                    <strong>Bus:</strong> {{ $trip->bus->name }}<br>
                                    <strong>License Plate:</strong> {{ $trip->bus->license_plate }}<br>
                                    <strong>Bus status:</strong> {{ $trip->bus->status }}<br>
                                    <strong>Departure:</strong> {{ \Carbon\Carbon::parse($trip->departure_time)->format('d/m/y H:i') }} <strong>At:</strong> {{ $trip->starting_location }}<br>
                                    <strong>Arrival:</strong> {{ \Carbon\Carbon::parse($trip->arrival_time)->format('d/m/y H:i') }} <strong>At:</strong> {{ $trip->destination }}<br>
                                    <strong>Total Seats:</strong> {{ $trip->bus->total_seats }}<br>
                                    <strong>Available Seats:</strong> {{ $trip->bus->available_seats }}<br>
                                    <strong>Available Seats after payment:</strong> {{ $trip->bus->available_seats - $quantity }}<br>
                                </li>
                        </ul>
                    @else
                        <p class="text-sm text-red-600">No trip information available for this festival.</p>
                    @endif

                    <form method="POST" action="{{ route('booking.create', ['festival_id' => $festival->id, 'quantity' => $quantity, 'trip_id' => $trip->id]) }}" class="mt-4">
                        @csrf
                        <label class="text-lg font-semibold mt-4">Got a reward you want to use?</label>

                        <select name="reward_id" class="w-full p-2 border border-gray-300 rounded" id="reward-select">
                            <option value="">Select a reward</option>
                            @foreach ($rewards as $reward)
                                <option value="{{ $reward->id }}" data-discount="{{ $reward->discount_percentage }}">{{ $reward->name }}</option>
                            @endforeach
                        </select>

                        <div id="new-total" class="mt-2 text-sm text-gray-600"></div>

                        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Pay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('reward-select').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const discountPercentage = selectedOption.getAttribute('data-discount');
            const totalPrice = {{ $totalPrice }};

            if (discountPercentage) {
                const discountAmount = totalPrice / 100 * discountPercentage;
                const newTotal = totalPrice - discountAmount;
                document.getElementById('new-total').textContent = `New total price after discount: €${newTotal.toFixed(2)}`;
            } else {
                document.getElementById('new-total').textContent = '';
            }
        });
    </script>
</x-app-layout>
