<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rewards') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-black">Available Rewards:</h1>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="text-gray-600">You currently have <strong>{{ auth()->user()->points }}</strong> points.</p>
            <p class="text-gray-600">Redeem your points for exciting rewards!</p>
        </div>
    </div>
    <div class="pb-12 pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach ($rewards as $reward)
                        <div class="border border-gray-200 mb-4 p-4 rounded">
                            <h3 class="text-lg font-semibold">{{ $reward->name }}</h3>
                            <p class="text-sm text-gray-600">Points Required: {{ $reward->points_required }}</p>
                            <p class="text-sm text-gray-600">Description: {{ $reward->description }}</p>

                            @if ($reward->image)
                                <img
                                    src="{{ asset($reward->image) }}"
                                    alt="{{ $reward->name }}"
                                    class="w-32 h-32 object-cover mt-2"
                                >
                            @else
                                <p class="text-sm text-gray-600">No image available</p>
                            @endif

                            @if (auth()->user()->points < $reward->points_required)
                                <button type="button" disabled class="px-4 py-2 bg-gray-300 text-white rounded hover:bg-gray-300">
                                    <p>Not enough points</p>
                                </button>
                            @elseif (auth()->user()->rewards()->where('rewards.id', $reward->id)->exists())
                                <button type="button" disabled class="px-4 py-2 bg-gray-300 text-white rounded hover:bg-gray-300">
                                    <p>Already redeemed</p>
                                </button>
                            @else
                                <form method="POST" action="{{ route('rewards.redeem', $reward->id) }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="reward_id" value="{{ $reward->id }}">
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Redeem
                                </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
