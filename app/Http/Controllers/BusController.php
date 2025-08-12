<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bus::query()->orderBy('created_at', 'desc');

        if (!empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $buses = $query->get();

        return view('admin.buses.index', [
            'buses' => $buses,
        ]);
    }

    public function create()
    {
        return view('admin.buses.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:10',
            'color' => 'nullable|string|max:30',
            'total_seats' => 'required|integer',
            'available_seats' => 'required|integer|min:0',
            'status' => 'required|in:inactive,available',
        ]);

        if ($validatedData['available_seats'] > $validatedData['total_seats']) {
            return redirect()->back()->withErrors(['available_seats' => 'Available seats cannot exceed total seats.']);
        }

        Bus::create($validatedData);

        return redirect()->route('admin.buses.index')->with('status', 'Bus created successfully!');
    }

    public function destroy($id)
    {
        if (!Bus::where('id', $id)->exists()) {
            return redirect()->route('admin.buses.index')->with('error', 'Bus not found.');
        }

        Bus::destroy($id);

        return redirect()->route('admin.buses.index')->with('status', 'Bus deleted successfully!');
    }
}
