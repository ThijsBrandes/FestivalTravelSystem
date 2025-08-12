<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-blue-100 p-4 rounded-lg shadow hover:bg-blue-200 transition">
                            <h3 class="text-lg font-semibold">Festivals</h3>
                            <p class="text-sm text-gray-600">Manage festivals</p>
                            <a href="{{ route('admin.festivals.index') }}" class="text-blue-600 hover:underline">View Festivals</a>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg shadow hover:bg-green-200 transition">
                            <h3 class="text-lg font-semibold">Bookings</h3>
                            <p class="text-sm text-gray-600">Manage bookings</p>
                            <a href="{{ route('admin.bookings.index') }}" class="text-green-600 hover:underline">View Bookings</a>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg shadow hover:bg-yellow-200 transition">
                            <h3 class="text-lg font-semibold">Rewards</h3>
                            <p class="text-sm text-gray-600">Manage rewards</p>
                            <a href="{{ route('admin.rewards.index') }}" class="text-yellow-600 hover:underline">View Rewards</a>
                        </div>
                        <div class="bg-red-100 p-4 rounded-lg shadow hover:bg-red-200 transition">
                            <h3 class="text-lg font-semibold">Users</h3>
                            <p class="text-sm text-gray-600">Manage users</p>
                            <a href="{{ route('admin.users.index') }}" class="text-red-600 hover:underline">View Users</a>
                        </div>

                        <div class="bg-purple-100 p-4 rounded-lg shadow hover:bg-purple-200 transition">
                            <h3 class="text-lg font-semibold">Buses</h3>
                            <p class="text-sm text-gray-600">Manage buses</p>
                            <a href="{{ route('admin.buses.index') }}" class="text-purple-600 hover:underline">View Buses</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
