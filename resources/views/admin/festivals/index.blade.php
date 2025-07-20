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
                    <div class="flex justify-between">
                        <!-- back button -->
                        <div class="mb-4">
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">
                                &larr; Back to Dashboard
                            </a>
                        </div>
                        <!-- create new festival button -->
                        <div class="mb-4">
                            <a href="{{ route('admin.festivals.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Create New Festival
                            </a>
                        </div>
                    </div>
                    <!-- admin festivals index -->
                    <form method="GET" action="{{ route('admin.festivals.index') }}" class="flex items-center space-x-4 mb-4">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search festivals..."
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
                            <form method="GET" action="{{ route('admin.festivals.index') }}" class="flex items-center space-x-4">
                                <button type="submit" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                    Clear Search
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($festivals as $festival)
                            <div class="border border-gray-200 p-4 rounded-lg shadow hover:shadow-lg transition">
                                <h3 class="text-lg font-semibold">{{ $festival->name }}</h3>
                                <p class="text-sm text-gray-600">Date: {{ $festival->date }}</p>
                                <p class="text-sm text-gray-600">Location: {{ $festival->location }}</p>

                                @if ($festival->is_active)
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Active</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Inactive</span>
                                @endif

                                <div class="mt-4">
                                    <a href="{{ route('admin.festivals.edit', $festival) }}" class="text-blue-600 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.festivals.destroy', $festival) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Are you sure you want to delete this festival?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>

                                    <form action="{{ route('admin.festivals.toggle', $festival) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:underline">
                                            {{ $festival->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
