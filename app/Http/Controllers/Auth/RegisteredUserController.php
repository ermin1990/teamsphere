<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:organizer,player'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->role === 'player') {
            Player::create([
                'name' => $user->name,
                'email' => $user->email,
                'user_id' => $user->id,
                'organization_id' => null,
            ]);
        }

        event(new Registered($user));

        $recipients = Setting::notificationRecipients();
        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new \App\Mail\NewUserRegistered($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send new user registration notification email', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('login')->with('status', 'Registracija je uspješna! Prijavite se sa vašim novim nalogom.');
    }
}
