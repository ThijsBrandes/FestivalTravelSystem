<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index(Request $request) {
        if (!empty($request->search)) {

            $request = $request->validate([
                'search' => 'nullable|string|max:255',
            ]);

            $rewards = Reward::Where(function ($query) use ($request) {
                if (!empty($request['search'])) {
                    $query->where('name', 'like', '%' . $request['search'] . '%');
                }
            })->paginate(10);
        } else {
            $rewards = Reward::all();
        }

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
}
