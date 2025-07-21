<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin buses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- back button -->
                    <div class="mb-4">
                        <a href="{{ route('admin.buses.index') }}" class="text-blue-600 hover:underline">
                            &larr; Back to Buses
                        </a>
                    </div>

                    <!-- Create bus form -->
                    <form method="POST" action="{{ route('admin.buses.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name (required)</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="license_plate" class="block text-sm font-medium text-gray-700">License plate (required)</label>
                            <input type="text" name="license_plate" id="license_plate" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="color" class="block text-sm font-medium text-gray-700">Color (optional)</label>
                            <input type="text" name="color" id="color" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="total_seats" class="block text-sm font-medium text-gray-700">Total Seats (required)</label>
                            <input type="number" name="total_seats" id="total_seats" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" oninput="validateSeats()">
                        </div>

                        <div class="mb-4">
                            <label for="available_seats" class="block text-sm font-medium text-gray-700">Available Seats (required)</label>
                            <input type="number" name="available_seats" id="available_seats" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" oninput="validateSeats()">
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status (required)</label>
                            <select name="status" class="border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                                <option value="available">Available</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Create Bus
                            </button>
                        </div>
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function validateSeats() {
        const totalSeats = document.getElementById('total_seats').value;
        const availableSeats = document.getElementById('available_seats').value;

        if (parseInt(availableSeats) > parseInt(totalSeats)) {
            alert('Available seats cannot be higher than total seats.');
            document.getElementById('available_seats').value = totalSeats;
        }
    }
</script>
