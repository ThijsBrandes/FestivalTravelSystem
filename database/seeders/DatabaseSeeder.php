<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Reward;
use App\Models\Booking;
use App\Models\Festival;
use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $bram = User::factory()->create([
            'name' => 'Bram',
            'email' => 'b.l.tukker@windesheim.nl',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'points' => 1000,
        ]);

        $users = User::factory(9)->create();

        // Festivals
        $festivals = Festival::factory(10)->create();

        // Rewards
        $rewards = Reward::factory(10)->create();

        // Bookings for each user
        $users->each(function ($user) use ($festivals) {
            Booking::factory(3)->create([
                'user_id' => $user->id,
                'festival_id' => $festivals->random()->id,
            ]);
        });

        // Bookings for Bram
        Booking::factory(5)->create([
            'user_id' => $bram->id,
            'festival_id' => $festivals->random()->id,
            'trip_id' => Trip::factory()->create([
                'user_id' => $bram->id,
                'bus_id' => Bus::factory()->create()->id,
                'starting_location' => 'Amsterdam',
                'destination' => 'Rotterdam',
                'departure_time' => now()->addDays(1),
                'arrival_time' => now()->addDays(1)->addHours(2),
            ])->id,
        ]);

        // Give Bram 2 rewards
        $bram->rewards()->attach(
            $rewards->random(2)->pluck('id'),
            ['redeemed_at' => now()]
        );

        // Create 5 buses
        $buses = Bus::factory(5)->create();

        // Create 20 trips for random users and buses
        User::inRandomOrder()->take(10)->get()->each(function ($user) use ($buses, $festivals) {
            $festival = $festivals->random();
            $festivalDate = \Carbon\Carbon::parse($festival->date);

            $departure = $festivalDate->copy()->subDay()->setTime(14, 0);
            $arrival = $festivalDate->copy()->setTime(10, 0); // Assume arrival in the morning

            // Pick a bus that hasn't been used for this festival yet
            $bus = $buses->first(function ($bus) use ($festival) {
                return !Trip::where('bus_id', $bus->id)
                    ->where('festival_id', $festival->id)
                    ->exists();
            });

            if ($bus) {
                Trip::factory()->create([
                    'user_id' => $user->id,
                    'bus_id' => $bus->id,
                    'festival_id' => $festival->id,
                    'starting_location' => fake()->city,
                    'destination' => $festival->location,
                    'departure_time' => $departure,
                    'arrival_time' => $arrival,
                ]);
            }
        });
    }
}
