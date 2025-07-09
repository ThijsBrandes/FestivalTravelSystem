<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $festival->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <img
                        src="{{ asset($festival->image ?? 'No image available') }}"
                        alt="{{ $festival->name }}"
                        class="w-full h-[300px] object-cover mt-2 rounded-lg"
                    >
                    <p class="text-md">{{ $festival->description }}</p>
                    <p class="text-sm text-gray-600">Location: {{ $festival->location }}</p>
                    <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($festival->date)->format('d/m/y H:i') }}</p>
                    <p class="mt-2 text-sm text-gray-600">Price per ticket: €{{ number_format($festival->price, 2) }}</p>
                    <!-- input field for quantity -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const quantityInput = document.getElementById('quantity');
                            quantityInput.addEventListener('input', function () {
                                const price = {{ $festival->price }};
                                const quantity = parseInt(quantityInput.value) || 1;
                                const totalPrice = (price * quantity).toFixed(2);
                                document.getElementById('total-price').innerHTML = `Total price: <strong>€${totalPrice}</strong>`;                            });
                        });
                    </script>

                    @guest
                        <button type="button" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            <a href="{{ route('login') }}" class="text-white">
                                Login to book
                            </a>
                        </button>
                    @endguest

                    @auth
                        @if ($availableSeats > 0)
                            <p class="mt-2 text-sm text-green-600">Bus available with {{ $availableSeats }} seats left.</p>
                            <form method="POST" action="{{ route('booking.create', ['festival_id' => $festival->id, 'user_id' => auth()->user()->id]) }}" class="mt-4">
                                @csrf
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity:</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    id="quantity"
                                    min="1"
                                    value="1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                <p id="total-price">Total price: <strong>€{{ $festival->price }}</strong></p>
                                <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Book Now
                                </button>
                            </form>

                        @elseif ($availableSeats === 0)
                            <p class="mt-2 text-sm text-red-600">No seats available for this festival.</p>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
