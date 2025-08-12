<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festival;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;

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

        $totalPrice = $festival->price * $request->quantity;
        $totalPoints = round($totalPrice, 0);

        $trip = Trip::where('festival_id', $festival->id)
            ->where('starting_location', $request->starting_location)
            ->whereHas('bus', function ($query) use ($request) {
                $query->where('available_seats', '>=', $request->quantity)
                    ->where('status', 'reserved');
            })->first();

        if (!$trip) {
            return redirect()->back()->withErrors(['error' => 'No available trip found for the selected (amount of) tickets.']);
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

        $festival = Festival::where('id', $request->festival_id)
            ->where('is_active', true)->first();

        if (!$festival) {
            return back()->withErrors(['error' => 'Festival is not active or does not exist.']);
        }

        $trip = Trip::with('bus')->findOrFail($request->trip_id);

        if (
            $trip->festival_id !== $festival->id
            || !$trip->bus
            || $trip->bus->status !== 'reserved'
            || $trip->bus->available_seats < $request->quantity
        ) {
            return back()->withErrors(['error' => 'No available trip found or not enough available seats for the selected trip.']);
        }

        $totalPrice  = $festival->price * $request->quantity;
        $totalPoints = (int) round($totalPrice, 0);

        $bus = $trip->bus;
        $bus->available_seats -= $request->quantity;

        if ($bus->available_seats < 0) {
            return back()->withErrors(['error' => 'No available trip found or not enough available seats for the selected trip.']);
        }

        if ($bus->available_seats == 0) {
            $bus->status = 'full';
        }

        $bus->save();

        $booking = Booking::create([
            'festival_id' => $festival->id,
            'user_id' => auth()->id(),
            'ticket_quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'total_points' => $totalPoints,
            'status' => 'confirmed',
            'trip_id' => $trip->id,
        ]);

        if ($request->reward_id) {
            $reward = auth()->user()->rewards()->wherePivot('used', false)->findOrFail($request->reward_id);
            $discount = $totalPrice * ($reward->discount_percentage / 100);
            $booking->total_price = $totalPrice - $discount;
            $booking->save();

            auth()->user()->rewards()->updateExistingPivot($reward->id, [
                'used'    => true,
                'used_at' => now(),
            ]);
        }

        $user = auth()->user();
        $user->points += $totalPoints;
        $user->save();

        return redirect()->route('bookings.show', ['booking' => $booking->id])
            ->with('status', 'Booking created successfully!');
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

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

    public function update(Request $request, Booking $booking)
    {
        if ($booking->status !== 'canceled' && Auth::id() === $booking->user->id) {
            $booking->trip->bus->available_seats += $booking->ticket_quantity;

            if ($booking->trip->bus->status === 'full' && $booking->trip->bus->available_seats > 0) {
                $booking->trip->bus->status = 'reserved';
            }

            $booking->trip->bus->save();

            $acquiredPoints = $booking->total_points;
            $user = Auth::user();

            $user->points -= $acquiredPoints;
            $user->save();

            $booking->status = 'canceled';
            $booking->save();

            return redirect()->route('bookings.show', ['booking' => $booking->id])
                             ->with('status', 'Booking canceled successfully!');
        }
        elseif ($booking->status === 'canceled') {
            return redirect()->route('bookings.show', ['booking' => $booking->id]);
        }
        else {
            abort(403, 'You are not authorized to cancel this booking.');
        }
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

        $bookings = $query->orderBy('booked_at', 'desc')->get();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
        ]);
    }

    public function reconfirm($id)
    {
        $booking = Booking::with('trip.bus')->findOrFail($id);

        if ($booking->status === 'confirmed') {
            return redirect()->route('admin.bookings.index')
                             ->withErrors(['error' => 'Booking is already confirmed.']);
        }

        $bus = $booking->trip->bus;

        if ($bus->available_seats < $booking->ticket_quantity) {
            return back()->withErrors(['error' => 'Not enough seats to reconfirm.']);
        }

        $bus->available_seats -= $booking->ticket_quantity;
        $bus->status = 'reserved';

        if ($bus->available_seats === 0) {
            $bus->status = 'full';
        }

        $bus->save();

        $user = $booking->user;
        $user->points += (int)$booking->total_points;
        $user->save();

        $booking->status = 'confirmed';
        $booking->save();

        return redirect()->route('admin.bookings.index')
            ->with('status', 'Booking reconfirmed successfully!');
    }

    public function destroy($id)
    {
        $booking = Booking::with('trip.bus')->findOrFail($id);

        if ($booking->status === 'canceled') {
            return redirect()->route('admin.bookings.index')
                             ->withErrors(['error' => 'Booking is already canceled.']);
        }

        $bus = $booking->trip->bus;
        $bus->available_seats += $booking->ticket_quantity;

        if ($bus->status !== 'reserved' && $bus->available_seats > 0) {
            $bus->status = 'reserved';
        }

        $bus->save();

        $user = $booking->user;
        $user->points -= (int)$booking->total_points;
        $user->save();

        $booking->status = 'canceled';
        $booking->save();

        return redirect()->route('admin.bookings.index')
                         ->with('status', 'Booking canceled successfully!');
    }
}
