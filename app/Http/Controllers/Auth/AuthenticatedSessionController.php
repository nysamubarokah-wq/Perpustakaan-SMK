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
   public function create(): RedirectResponse|View
{
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('buku.index');
        }
        return redirect()->route('dashboard');
    }
    
    return view('auth.login');
}

    /**
     * Handle an incoming authentication request.
     */
  public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Generate new token for rules modal (only on actual login)
    auth()->user()->update(['rules_session_token' => \Illuminate\Support\Str::random(32)]);

   if (auth()->user()->role === 'admin') {
    return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
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
