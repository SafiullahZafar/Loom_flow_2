<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Welcome back, '.Auth::user()->name.'!');
            }
        } catch (Throwable $exception) {
            Log::error('Sign-in failed because of a server error.', [
                'exception' => $exception,
            ]);

            return back()->withInput($request->only('email'))->with('error', 'Sign-in is temporarily unavailable because of a server problem. Please try again.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request): RedirectResponse
    {
        abort_unless(app()->environment('local') && config('app.debug'), 404);
        $email = $request->input('email', 'admin@loomflow.com');
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'Logged in as '.$user->name.' ('.ucfirst($user->role).')');
        }

        return redirect()->route('login')->with('error', 'User not found.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
