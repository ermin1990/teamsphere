<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Setting;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * "Sign in with Google" via Firebase Authentication. The frontend (login/
 * register pages) runs Firebase's signInWithPopup(GoogleAuthProvider) and
 * posts the resulting Firebase ID token here - we verify it server-side
 * (FirebaseAuthService, no Firebase Admin SDK/service account needed) and
 * either log the matching existing user in, or - for a brand new email -
 * park the verified profile in the session and send them to a short
 * "choose your role" step before the account is actually created, mirroring
 * the role choice normal registration already requires.
 */
class GoogleAuthController extends Controller
{
    private const SESSION_KEY = 'google_pending_signup';

    public function __construct(private readonly FirebaseAuthService $firebaseAuth)
    {
    }

    /**
     * Verify the posted Firebase ID token and either log the user in
     * (existing account, matched by email or by a previously linked
     * google_id) or start the "choose role" flow for a brand new signup.
     */
    public function handle(Request $request): RedirectResponse
    {
        $request->validate(['id_token' => ['required', 'string']]);

        $claims = $this->firebaseAuth->verify($request->input('id_token'));

        if (!$claims || empty($claims['email'])) {
            return redirect()->route('login')->with('error', 'Google prijava nije uspjela. Pokušajte ponovo.');
        }

        if (empty($claims['email_verified'])) {
            return redirect()->route('login')->with('error', 'Vaš Google email nije verifikovan.');
        }

        $googleId = $claims['sub'];
        $email = $claims['email'];
        $name = $claims['name'] ?? explode('@', $email)[0];
        $avatar = $claims['picture'] ?? null;

        $user = User::where('google_id', $googleId)->orWhere('email', $email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->forceFill(['google_id' => $googleId, 'avatar' => $user->avatar ?? $avatar])->save();
            }
            if (!$user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            $home = $user->needsOrganizationOnboarding()
                ? route('organizations.create', absolute: false)
                : ($user->isOrganizerOrStaff()
                    ? route('dashboard', absolute: false)
                    : route('player.dashboard', absolute: false));

            return redirect()->intended($home);
        }

        session([self::SESSION_KEY => [
            'google_id' => $googleId,
            'email' => $email,
            'name' => $name,
            'avatar' => $avatar,
        ]]);

        return redirect()->route('google.choose-role');
    }

    /**
     * Ask a brand new Google signup whether they're a player or an
     * organizer - the same choice the normal registration form makes them
     * pick, just deferred until after Google has verified their identity.
     */
    public function showChooseRole(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (!$pending) {
            return redirect()->route('register');
        }

        return view('auth.google-choose-role', ['pending' => $pending]);
    }

    /**
     * Create the account for a brand new Google signup now that they've
     * picked a role, and log them in.
     */
    public function completeRegistration(Request $request): RedirectResponse
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (!$pending) {
            return redirect()->route('register');
        }

        $request->validate(['role' => ['required', 'in:organizer,player']]);

        // Someone could have registered with this email in the time between
        // the Google popup and submitting this form - fall back to the
        // normal login flow for them instead of violating the unique index.
        if (User::where('email', $pending['email'])->exists()) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('login')->with('error', 'Nalog sa ovim emailom već postoji. Prijavite se.');
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'google_id' => $pending['google_id'],
            'avatar' => $pending['avatar'],
        ]);

        // Google already verified this email - email_verified_at isn't
        // mass-assignable, so it's set explicitly here rather than via create().
        $user->forceFill(['email_verified_at' => now()])->save();

        if ($request->input('role') === 'player') {
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

        $request->session()->forget(self::SESSION_KEY);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $home = $request->input('role') === 'organizer'
            ? route('organizations.create', absolute: false)
            : route('player.dashboard', absolute: false);

        return redirect($home);
    }
}
