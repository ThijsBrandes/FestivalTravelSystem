<?php

namespace App\Http\Controllers;

use App\Models\Bus;
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
            if (!$bus || $bus->status !== 'reserved') {
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

            $festivals = $query->get()->map(function ($festival) {
                $festival->availableSeats = $this->availableSeats($festival);
                return $festival;
            });

            return view('festivals.index', [
                'festivals' => $festivals,
            ]);
        }

        if (!$request->has('show_inactive') && empty($request->search)) {
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

    public function adminIndex(Request $request)
    {
        $query = Festival::query()->orderBy('created_at', 'desc');

        if (!empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $festivals = $query->get();

        return view('admin.festivals.index', [
            'festivals' => $festivals,
        ]);
    }

    public function create()
    {
        $buses = Bus::where('status', 'available')->get();

        return view('admin.festivals.create', [
            'buses' => $buses,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        $validatedData['image'] = $request->file('image')->store('festival-images', 'public');
        $validatedData['image'] = 'storage/' . $validatedData['image'];

        $festival = Festival::create($validatedData);

        // Handle trips
        $trips = $request->input('trips', []);

        foreach ($trips as $tripData) {
            $bus = \App\Models\Bus::findOrFail($tripData['bus_id']);

            if ($bus->status !== 'available') {
                return back()->withErrors(['bus' => "Bus {$bus->name} is no longer available."]);
            }

            \App\Models\Trip::create([
                'bus_id' => $bus->id,
                'festival_id' => $festival->id,
                'starting_location' => $tripData['starting_location'],
                'departure_time' => $tripData['departure_time'],
                'arrival_time' => $tripData['arrival_time'],
            ]);

            $bus->update(['status' => 'reserved']);
        }

        return redirect()->route('admin.festivals.index')
            ->with('status', 'Festival and trips created successfully.');
    }

    public function edit(Festival $festival)
    {
        return view('admin.festivals.edit', [
            'festival' => $festival,
        ]);
    }

    public function update(Request $request, Festival $festival)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('festival-images', 'public');

            if ($festival->image) {
                \Storage::disk('public')->delete($festival->image);
            }

            $validatedData['image'] = 'storage/' . $validatedData['image'];
        }

        if ($validatedData['location']) {
            Trip::where('festival_id', $festival->id)
                ->update(['destination' => $validatedData['location']]);
        }

        $festival->update($validatedData);

        return redirect()->route('admin.festivals.index')
            ->with('status', 'Festival updated successfully.');
    }

    public function destroy(Festival $festival)
    {
        $festival->delete();

        return redirect()->route('admin.festivals.index')
            ->with('status', 'Festival deleted successfully.');
    }

    public function toggle(Festival $festival)
    {
        if (!$festival) {
            return redirect()->route('admin.festivals.index')
                ->with('error', 'Festival not found.');
        }

        $festival->is_active = !$festival->is_active;
        $festival->save();

        return redirect()->route('admin.festivals.index')
            ->with('status', 'Festival status updated successfully.');
    }
}
