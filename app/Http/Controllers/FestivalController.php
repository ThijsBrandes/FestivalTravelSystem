<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Models\Trip;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    public function index(Request $request)
    {
        if (!empty($request->search)) {
            $festivals = Festival::where('name', 'like', '%' . $request->search . '%')
                ->orWhere('location', 'like', '%' . $request->search . '%')
                ->get();
        } else {
            // If no search term is provided, retrieve all festivals
            $festivals = Festival::all();
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
        $trips = Trip::where('festival_id', $festival->id)->get();

        $buses = $trips->map(function ($trip) {
            return $trip->bus;
        });

        $availableSeats = 0;

        foreach ($buses as $bus) {
            $availableSeats += $bus->available_seats;
        }

        return view('festivals.show', [
            'festival' => $festival,
            'availableSeats' => $availableSeats,
        ]);
    }
}
