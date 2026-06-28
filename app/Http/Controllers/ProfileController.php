<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    // ── AFFICHAGE DU PROFIL ───────────────────────────────────────────────
    public function show()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->load('roles', 'staff');

        return view('profile.show', compact('user'));
    }

    /**
     * Display the user's profile form.
     */
    // public function edit(Request $request): View
    // {
    //     return view('profile.edit', [
    //         'user' => $request->user(),
    //     ]);
    // }

    // ── FORMULAIRE DE MODIFICATION ────────────────────────────────────────
    public function edit()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->load('staff');

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }
    // ── MISE À JOUR ────────────────────────────────────────────────────────
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Vérifier le mot de passe actuel si changement demandé
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Le mot de passe actuel est incorrect.',
                ])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users/photos', 'public');
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    // ── SUPPRIMER LA PHOTO ─────────────────────────────────────────────────
    public function deletePhoto()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->update(['photo' => null]);
        }
        return back()->with('success', 'Photo supprimée.');
    }

    // ── DÉCONNECTER LES AUTRES SESSIONS ───────────────────────────────────
    // public function logoutOtherSessions(Request $request)
    // {
    //     $request->validate([
    //         'password' => ['required', 'current_password'],
    //     ]);

    //     auth()->guard('web')->logoutOtherDevices($request->password);

    //     return back()->with('success', 'Toutes les autres sessions ont été déconnectées.');
    // }

    /**
     * Delete the user's account.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $request->validateWithBag('userDeletion', [
    //         'password' => ['required', 'current_password'],
    //     ]);

    //     $user = $request->user();

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/');
    // }
}
