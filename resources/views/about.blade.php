<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">
                        FTS is built to make traveling to festivals across Europe easier, smarter, and more enjoyable. Whether you're heading to a weekend rave or a multi-day outdoor event, we take care of the ride so you can focus on the experience.
                    </p>

                    <p class="mb-2">
                        Our platform brings everything into one place:
                    </p>
                    <ul class="list-disc list-inside mb-4 space-y-1">
                        <li><span class="font-semibold">Easy trip booking</span> with clear travel info</li>
                        <li><span class="font-semibold">Personal account dashboard</span> to track bookings and rewards</li>
                        <li><span class="font-semibold">Festival management tools</span> for organizers and planners</li>
                    </ul>

                    <p class="mb-4">
                        Once a festival reaches enough bookings, a bus is automatically scheduled. This keeps things efficient and reliable.
                    </p>

                    <p class="mb-4">
                        We also believe in rewarding loyalty. The more you travel with us, the more benefits you'll unlock.
                        Check out <a href="{{ route('rewards.index') }}" class="text-blue-600 underline">what rewards await you</a>.
                    </p>

                    <p class="mb-4">
                        FTS is designed to grow with you. In the future, expect mobile access, connections to external ticket platforms, and even more personalization.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
