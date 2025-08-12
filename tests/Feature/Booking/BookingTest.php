<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Festival;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_booking_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
    }

    public function test_booking_can_be_created(): void
    {
        $user = User::factory()->create([
            'points' => 0,
        ]);

        $festival = Festival::factory()->create([
            'name' => 'Test Festival',
            'is_active' => true,
            'price' => 100,
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-5',
            'status' => 'available',
            'total_seats' => 50,
            'available_seats' => 50,
        ]);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'festival_id' => $festival->id,
            'starting_location' => 'City A',
            'destination' => $festival->location,
            'departure_time' => now()->addDays(1),
            'arrival_time' => now()->addDays(2),
        ]);

        $quantity = 2;

        $response = $this
            ->actingAs($user)
            ->post('/create-booking', [
                'festival_id' => $festival->id,
                'trip_id' => $trip->id,
                'reward_id' => null,
                'quantity' => $quantity,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/bookings/1');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'festival_id' => $festival->id,
            'trip_id' => $trip->id,
            'ticket_quantity' => 2,
            'total_price' => $festival->price * $quantity,
            'total_points' => round($festival->price * $quantity, 0),
            'status' => 'confirmed',
        ]);

        $booking = Booking::where('user_id', $user->id)->first();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'points' => $booking->total_points,
        ]);
    }

    public function test_booking_fails_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/create-booking', []);

        $response->assertSessionHasErrors(['festival_id', 'trip_id', 'quantity']);
    }

    public function test_booking_can_be_canceled(): void
    {
        $user = User::factory()->create([
            'points' => 200, // Assuming user has made a booking and points have been awarded
        ]);

        $festival = Festival::factory()->create([
            'is_active' => true,
            'price' => 100,
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-5',
            'status' => 'available',
            'total_seats' => 50,
            'available_seats' => 50,
        ]);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'festival_id' => $festival->id,
            'starting_location' => 'City A',
            'destination' => $festival->location,
            'departure_time' => now()->addDays(1),
            'arrival_time' => now()->addDays(2),
        ]);

        $quantity = 2;

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'festival_id' => $festival->id,
            'trip_id' => $trip->id,
            'total_price' => $festival->price * $quantity,
            'total_points' => round($festival->price * $quantity, 0),
            'status' => 'confirmed',
            'ticket_quantity' => $quantity,
        ]);

        $response = $this
            ->actingAs($user)
            ->post("/bookings/{$booking->id}", [
                'status' => 'canceled',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect("/bookings/{$booking->id}");

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'canceled',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'points' => 0, // Points should be removed on cancellation
        ]);
    }
}
