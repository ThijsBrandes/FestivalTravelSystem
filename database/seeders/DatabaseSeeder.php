<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Reward;
use App\Models\Booking;
use App\Models\Festival;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\FestivalImage;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\FestivalImageFactory;
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
        ]);

        // Give Bram 2 rewards
        $bram->rewards()->attach(
            $rewards->random(2)->pluck('id'),
            ['redeemed_at' => now()]
        );

        // Create 5 buses
        $buses = Bus::factory(5)->create();

        // Create 20 trips for random users and buses
        User::inRandomOrder()->take(10)->get()->each(function ($user) use ($buses) {
            Trip::factory(2)->create([
                'user_id' => $user->id,
                'bus_id' => $buses->random()->id,
            ]);
        });

    }
}
