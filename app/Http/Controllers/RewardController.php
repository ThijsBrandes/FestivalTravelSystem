<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index(Request $request) {
        $user = auth()->user();

        if (!empty($request->search)) {
            $validatedData = $request->validate([
                'search' => 'nullable|string|max:255',
            ]);
            $rewards = Reward::where(function ($query) use ($validatedData) {
                if (!empty($validatedData['search'])) {
                    $query->where('name', 'like', '%' . $validatedData['search'] . '%');
                }
            })->get();
        } else {
            $rewards = Reward::all();
        }

        $redeemedRewards = $user->rewards()->pluck('reward_id')->toArray();

        $rewards = $rewards->sortBy(function ($reward) use ($redeemedRewards) {
            return in_array($reward->id, $redeemedRewards) ? 0 : 1;
        });

        return view('rewards.index', [
            'rewards' => $rewards,
        ]);
    }

    public function redeem(Request $request, $id) {
        $reward = Reward::findOrFail($id);
        $user = auth()->user();

        if ($user->points < $reward->points_required) {
            return redirect()->back()->with('error', 'Not enough points to redeem this reward.');
        }

        $user->points -= $reward->points_required;
        $user->save();

        auth()->user()->rewards()->attach($reward->id, ['redeemed_at' => now()]);

        return redirect()->route('rewards.index')->with('status', 'Reward redeemed successfully!');
    }

    public function adminIndex(Request $request) {
        $query = Reward::query();

        if (!empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $rewards = $query->orderBy('created_at', 'desc')->get();

        return view('admin.rewards.index', [
            'rewards' => $rewards,
        ]);
    }

    public function create() {
        return view('admin.rewards.create');
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'points_required' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:1',
        ]);

        Reward::create($validatedData);

        return redirect()->route('admin.rewards.index')->with('status', 'Reward created successfully!');
    }

    public function edit(Reward $reward) {
        return view('admin.rewards.edit', [
            'reward' => $reward,
        ]);
    }

    public function update(Request $request, Reward $reward) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'points_required' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:1',
        ]);

        $reward->update($validatedData);

        return redirect()->route('admin.rewards.index')->with('status', 'Reward updated successfully!');
    }

    public function destroy(Reward $reward) {
        $reward->delete();

        return redirect()->route('admin.rewards.index')->with('status', 'Reward deleted successfully!');
    }
}
