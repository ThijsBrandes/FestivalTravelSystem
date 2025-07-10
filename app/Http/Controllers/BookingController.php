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

        $trips = Trip::where('festival_id', $festival->id)->get();

        $selectedTrip = $trips->first(function ($trip) use ($request) {
            return $trip->bus && $trip->bus->available_seats >= $request->quantity;
        });

        if (!$selectedTrip) {
            return redirect()->back()->withErrors(['error' => 'No available trip found for the selected festival.']);
        }

        $bus = $selectedTrip->bus;
        $bus->available_seats -= $request->quantity;
        $bus->save();

        $booking = Booking::create([
            'festival_id' => $request->festival_id,
            'user_id' => $request->user_id,
            'ticket_quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'trip_id' => $selectedTrip->id,
        ]);

        if ($booking) {
            $booking->status = 'confirmed';
            $booking->save();
        } else {
            return redirect()->back()->withErrors(['error' => 'Booking could not be created.']);
        }

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

    public function index(Request $request)
    {
        if (!empty($request->search)) {
            $searchTerm = $request->input('search');

            $bookings = Booking::where('user_id', auth()->id())
                ->where(function ($query) use ($searchTerm) {
                    $query->whereHas('festival', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    })->orWhere('id', 'like', '%' . $searchTerm . '%');
                })->get();
        } else {
            $bookings = Booking::where('user_id', auth()->id())->get();
        }

        return view('dashboard', [
            'bookings' => $bookings,
        ]);
    }
}
