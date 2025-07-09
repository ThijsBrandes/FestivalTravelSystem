<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festival;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Trip;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $festival = Festival::findOrFail($request->festival_id);
        $totalPrice = $festival->price * $request->quantity;

        $booking = Booking::create([
            'festival_id' => $request->festival_id,
            'user_id' => $request->user_id,
            'ticket_quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        if ($booking) {
            $booking->status = 'confirmed';
            $booking->save();
        } else {
            return redirect()->back()->withErrors(['error' => 'Booking could not be created.']);
        }

        $trips = Trip::where('festival_id', $festival->id)->get();

        $bus = $trips->map(function ($trip) {
            return $trip->bus;
        })->where('available_seats', '>', $request->quantity)->first();

        if (!$bus) {
            return redirect()->back()->withErrors(['error' => 'No available bus found for the selected festival.']);
        }

        $bus->available_seats = $bus->available_seats + $request->quantity;

        Bus::update($bus);

        return redirect()->route('bookings.show', ['booking' => $booking->id])
                         ->with('status', 'Booking created successfully!');
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        return view('bookings.show', [
            'booking' => $booking,
        ]);
    }
}
