<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the admin user management page.
     */
    public function adminIndex(Request $request): View
    {
        $query = \App\Models\User::query();

        if (!empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Display the form for editing a specific user.
     */
    public function adminEdit($id): View
    {
        $user = \App\Models\User::findOrFail($id);

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update a specific user's information.
     */
    public function adminUpdate(Request $request, $id): RedirectResponse
    {
        $user = \App\Models\User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
        ]);

        $user->fill($request->only('name', 'email', 'role'));
        $user->save();

        return Redirect::route('admin.users.index')->with('status', 'User updated successfully.');
    }

    /**
     * Delete a specific user.
     */
    public function adminDestroy($id): RedirectResponse
    {
        $user = \App\Models\User::findOrFail($id);

        // Ensure the user cannot delete themselves
        if ($user->id === Auth::id()) {
            return Redirect::route('admin.users.index')->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return Redirect::route('admin.users.index')->with('status', 'User deleted successfully.');
    }
}
