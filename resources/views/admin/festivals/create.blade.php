<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin festivals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- back button -->
                    <div class="mb-4">
                        <a href="{{ route('admin.festivals.index') }}" class="text-blue-600 hover:underline">
                            &larr; Back to festivals
                        </a>
                    </div>
                    <!-- Form to create a festival -->
                    <form method="POST" action="{{ route('admin.festivals.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="description" id="description" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="location" id="location" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="datetime-local" name="date" id="date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Price (€)</label>
                            <input type="number" name="price" id="price" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.svg" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>

                        <div id="trips-container" class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Trips</h3>
                            <button type="button" onclick="addTrip()" class="mb-4 text-blue-600 hover:underline">+ Add Trip</button>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Create Festival
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
    let tripIndex = 0;
    let buses = @json($buses);

    function getSelectedBusIds() {
        const selects = document.querySelectorAll('select[name^="trips"][name$="[bus_id]"]');
        return Array.from(selects).map(s => s.value).filter(v => v);
    }

    function addTrip() {
        const selectedBusIds = getSelectedBusIds();
        const available = buses.filter(bus => !selectedBusIds.includes(String(bus.id)));

        if (available.length === 0) {
            alert("No more available buses.");
            return;
        }

        const container = document.getElementById('trips-container');
        const div = document.createElement('div');
        div.classList.add('mb-4', 'border', 'p-3', 'rounded', 'relative');

        let busOptions = available.map(bus =>
            `<option value="${bus.id}">${bus.license_plate} (Available seats: ${bus.available_seats})</option>`
        ).join('');

        div.innerHTML = `
        <label class="block mb-1">Starting Location</label>
        <input type="text" name="trips[${tripIndex}][starting_location]" required class="block mb-2 w-full border rounded p-2" />

        <label class="block mb-1">Departure Time</label>
        <input type="datetime-local" name="trips[${tripIndex}][departure_time]" required class="block mb-2 w-full border rounded p-2" />

        <label class="block mb-1">Arrival Time</label>
        <input type="datetime-local" name="trips[${tripIndex}][arrival_time]" required class="block mb-2 w-full border rounded p-2" />

        <label class="block mb-1">Bus</label>
        <select name="trips[${tripIndex}][bus_id]" required class="block mb-6 w-full border rounded p-2">
            ${busOptions}
        </select>

        <button type="button" onclick="removeTrip(this)" class="absolute bottom-2 right-4 text-red-600 hover:underline text-sm">Delete trip</button>
    `;

        container.appendChild(div);
        tripIndex++;
        refreshBusDropdowns();
    }

    function removeTrip(button) {
        button.parentElement.remove();
        refreshBusDropdowns();
    }

    function refreshBusDropdowns() {
        const selectedBusIds = getSelectedBusIds();
        const selects = document.querySelectorAll('select[name^="trips"][name$="[bus_id]"]');

        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = "";

            buses.forEach(bus => {
                const isUsed = selectedBusIds.includes(String(bus.id));
                const isCurrent = String(bus.id) === currentValue;

                if (!isUsed || isCurrent) {
                    const option = document.createElement('option');
                    option.value = bus.id;
                    option.text = `${bus.license_plate} (Available seats: ${bus.available_seats})`;
                    if (isCurrent) option.selected = true;
                    select.appendChild(option);
                }
            });
        });
    }
</script>
