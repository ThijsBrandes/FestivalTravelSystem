<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festival;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Trip;

class BookingController extends Controller
{
    public function preview(Request $request) {
        $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'quantity' => 'required|integer|min:1',
            'starting_location' => 'required|string|max:255',
        ]);

        $festival = Festival::findOrFail($request->festival_id);

        if (!$festival->is_active) {
            return redirect()->back()->withErrors(['error' => 'Festival is not active.']);
        }

        $startingLocation = $request->starting_location;

        $totalPrice = $festival->price * $request->quantity;
        $totalPoints = round($totalPrice, 0);

        $trip = Trip::where('festival_id', $festival->id)
            ->where('starting_location', $request->starting_location)
            ->whereHas('bus', function ($query) use ($request) {
                $query->where('available_seats', '>=', $request->quantity)
                    ->where('status', 'available');
            })->first();

        if (!$trip) {
            return redirect()->back()->withErrors(['error' => 'No available trip found for the selected festival.']);
        }

        $rewards = auth()->user()->rewards()->where('used', false)->get();

        return view('bookings.preview', [
            'festival' => $festival,
            'trip' => $trip,
            'quantity' => $request->quantity,
            'totalPrice' => $totalPrice,
            'totalPoints' => $totalPoints,
            'rewards' => $rewards,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'quantity' => 'required|integer|min:1',
            'trip_id' => 'required|exists:trips,id',
            'reward_id' => 'nullable|exists:rewards,id',
        ]);

        $festival = Festival::findOrFail($request->festival_id);
        $totalPrice = $festival->price * $request->quantity;
        $totalPoints = round($totalPrice, 0);

        if ($request->reward_id) {
            $reward = auth()->user()->rewards()->findOrFail($request->reward_id);

            $discountAmountInEuros = $totalPrice / 100 * $reward->discount_percentage;

            $totalPrice -= $discountAmountInEuros;

            auth()->user()->rewards()->updateExistingPivot($reward->id, [
                'used' => true,
                'used_at' => now(),
            ]);
        }

        $user = auth()->user();
        $user->points += $totalPoints;
        $user->save();

        $trips = Trip::where('festival_id', $festival->id)->get();

        $selectedTrip = $trips->first(function ($trip) use ($request) {
            return $trip->bus && $trip->bus->available_seats >= $request->quantity && $trip->bus->status === 'available';
        });

        if (!$selectedTrip) {
            return redirect()->back()->withErrors(['error' => 'No available trip found for the selected festival.']);
        }

        $bus = $selectedTrip->bus;
        $bus->available_seats -= $request->quantity;

        if ($bus->available_seats <= 0) {
            $bus->status = 'full';
        }

        $bus->save();

        $booking = Booking::create([
            'festival_id' => $request->festival_id,
            'user_id' => auth()->id(),
            'ticket_quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'total_points' => $totalPoints,
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

        $booking->reward = auth()->user()->rewards()
            ->where('used', true)
            ->whereBetween('used_at', [$booking->created_at->subSeconds(3), $booking->created_at->addSeconds(3)])
            ->first();

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
                })->orderBy('booked_at', 'desc')->get();
        } else {
            $bookings = Booking::where('user_id', auth()->id())
                ->orderBy('booked_at', 'desc')->get();
        }

        return view('dashboard', [
            'bookings' => $bookings,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = Booking::query();

        if (!empty($request->search)) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('festival', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                })->orWhere('id', 'like', '%' . $searchTerm . '%');
            });
        }

        $bookings = $query->orderBy('booked_at', 'desc')->paginate(20);

        return view('admin.bookings.index', [
            'bookings' => $bookings,
        ]);
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);

        return view('admin.bookings.edit', [
            'booking' => $booking,
        ]);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return redirect()->route('admin.bookings.index')
                         ->with('status', 'Booking status updated successfully!');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')
                         ->with('status', 'Booking deleted successfully!');
    }
}
