<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Models\Trip;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    private function availableSeats($festival)
    {
        $trips = Trip::where('festival_id', $festival->id)
            ->where('destination', $festival->location)
            ->get();

        $buses = $trips->map(function ($trip) {
            return $trip->bus;
        })->unique('id');

        $availableSeats = 0;

        foreach ($buses as $bus) {
            if (!$bus || $bus->status !== 'available') {
                continue;
            }

            $availableSeats += $bus->available_seats;
        }

        return $availableSeats;
    }

    private function trips($festival)
    {
        return Trip::where('festival_id', $festival->id)
            ->where('destination', $festival->location)
            ->with('bus')
            ->get();
    }

    public function index(Request $request)
    {
        $query = Festival::query();

        if (!empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if (!$request->has('show_inactive')) {
            $query->where('is_active', true);

            $festivals = $query->get()->filter(function ($festival) {
                return $this->availableSeats($festival) > 0;
            })->map(function ($festival) {
                $festival->availableSeats = $this->availableSeats($festival);
                return $festival;
            });
        } else {
            $query->where('is_active', false);

            $festivals = $query->get()->map(function ($festival) {
                $festival->availableSeats = $this->availableSeats($festival);
                return $festival;
            });
        }

        return view('festivals.index', [
            'festivals' => $festivals,
        ]);
    }

    public function home()
    {
        $festivals = Festival::all()->take(4);

        return view('welcome', [
            'festivals' => $festivals,
        ]);
    }

    public function show(Festival $festival)
    {
        $availableSeats = $this->availableSeats($festival);

        $trips = $this->trips($festival);

        return view('festivals.show', [
            'festival' => $festival,
            'availableSeats' => $availableSeats,
            'trips' => $trips,
        ]);
    }
}
