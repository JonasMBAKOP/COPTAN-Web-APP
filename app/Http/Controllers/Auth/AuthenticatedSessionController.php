<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $activeYear = \Illuminate\Support\Facades\DB::table('academic_years')
                    ->where('is_active', true)
                    ->first();

        return view('auth.login', compact('activeYear'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier si le compte est actif
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ]);
        }

        // Mettre à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        // Redirection selon le rôle
        return redirect($this->redirectBasedOnRole($user));
    }


    private function redirectBasedOnRole($user): string
    {
        return match(true) {
            $user->hasRole('super-admin')         => route('admin.dashboard'),
            $user->hasRole('directeur')           => route('directeur.dashboard'),
            $user->hasRole('censeur')             => route('censeur.dashboard'),
            $user->hasRole('econome')             => route('econome.dashboard'),
            $user->hasRole('enseignant')          => route('enseignant.dashboard'),
            $user->hasRole('surveillant-general') => route('surveillant.dashboard'),
            default                               => route('login'),
        };
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
