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

    public function test_dashboard_is_displayed(): void
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
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-5',
            'status' => 'reserved',
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

        $response->assertSessionHasNoErrors();

        $booking = Booking::first();
        $this->assertNotNull($booking);

        $response->assertRedirect("/bookings/{$booking->id}");

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'user_id' => $user->id,
            'festival_id' => $festival->id,
            'trip_id' => $trip->id,
            'ticket_quantity' => $quantity,
            'total_price' => $festival->price * $quantity,
            'total_points' => round($festival->price * $quantity, 0),
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'points' => $booking->total_points,
        ]);

        $this->assertDatabaseHas('buses', [
            'id' => $bus->id,
            'available_seats' => 50 - $quantity,
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

    public function test_booking_cannot_be_created_for_inactive_festival(): void
    {
        $user = User::factory()->create();

        $festival = Festival::factory()->create([
            'is_active' => false,
            'price' => 100,
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-6',
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

        $response = $this
            ->actingAs($user)
            ->post('/create-booking', [
                'festival_id' => $festival->id,
                'trip_id' => $trip->id,
                'quantity' => 1,
            ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_fails_when_quantity_exceeds_available_seats(): void
    {
        $user = User::factory()->create();

        $festival = Festival::factory()->create([
            'is_active' => true,
            'price' => 100,
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-7',
            'status' => 'available',
            'total_seats' => 50,
            'available_seats' => 10,
        ]);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'festival_id' => $festival->id,
            'starting_location' => 'City A',
            'destination' => $festival->location,
            'departure_time' => now()->addDays(1),
            'arrival_time' => now()->addDays(2),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/create-booking', [
                'festival_id' => $festival->id,
                'trip_id' => $trip->id,
                'quantity' => 11,
            ]);

        $response->assertSessionHasErrors([
            'error' => 'No available trip found or not enough available seats for the selected trip.',
        ]);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_can_be_canceled(): void
    {
        $user = User::factory()->create([
            'points' => 200,
        ]);

        $festival = Festival::factory()->create([
            'is_active' => true,
            'price' => 100,
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-8',
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

        $bus->update(['available_seats' => 50 - $quantity]);

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
            'points' => 0,
        ]);

        $this->assertDatabaseHas('buses', [
            'id' => $bus->id,
            'available_seats' => 50,
        ]);
    }

    public function test_only_owner_can_cancel_booking(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $festival = Festival::factory()->create([
            'is_active' => true,
            'price' => 50,
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-34-9',
            'status' => 'available',
            'total_seats' => 40,
            'available_seats' => 40,
        ]);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'festival_id' => $festival->id,
            'starting_location' => 'City B',
            'destination' => $festival->location,
            'departure_time' => now()->addDays(3),
            'arrival_time' => now()->addDays(4),
        ]);

        $booking = Booking::factory()->create([
            'user_id' => $owner->id,
            'festival_id' => $festival->id,
            'trip_id' => $trip->id,
            'ticket_quantity' => 1,
            'total_price' => 50,
            'total_points' => 50,
            'status' => 'confirmed',
        ]);

        $this->actingAs($other)
            ->post("/bookings/{$booking->id}", ['status' => 'canceled'])
            ->assertForbidden();
    }

    public function test_cancel_is_idempotent(): void
    {
        $user = User::factory()->create(['points' => 200]);

        $festival = Festival::factory()->create([
            'is_active' => true,
            'price' => 100,
            'location' => 'Festival Grounds',
        ]);

        $bus = Bus::factory()->create([
            'name' => 'Test Bus',
            'license_plate' => '12-35-0',
            'status' => 'available',
            'total_seats' => 50,
            'available_seats' => 48,
        ]);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'festival_id' => $festival->id,
            'starting_location' => 'City C',
            'destination' => $festival->location,
            'departure_time' => now()->addDays(1),
            'arrival_time' => now()->addDays(2),
        ]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'festival_id' => $festival->id,
            'trip_id' => $trip->id,
            'ticket_quantity' => 2,
            'total_price' => 200,
            'total_points' => 200,
            'status' => 'confirmed',
        ]);

        $first = $this->actingAs($user)->post("/bookings/{$booking->id}", ['status' => 'canceled']);
        $first->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'points' => 0]);
        $this->assertDatabaseHas('buses', ['id' => $bus->id, 'available_seats' => 50]);

        $second = $this->actingAs($user)->post("/bookings/{$booking->id}", ['status' => 'canceled']);
        $second->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'points' => 0]);
        $this->assertDatabaseHas('buses', ['id' => $bus->id, 'available_seats' => 50]);
    }
}
