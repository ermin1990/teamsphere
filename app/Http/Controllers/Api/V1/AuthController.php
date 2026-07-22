<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Player;
use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponses;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful');
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('web')->validate($credentials)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();
        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->ok([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Exchange a verified Firebase ID token (from "Sign in with Google" on
     * the mobile app) for a Sanctum token - mirrors the web
     * Auth\GoogleAuthController flow, but stateless: a brand new signup
     * gets `data.needs_role = true` back with their Google profile so the
     * app can show a role picker and call this endpoint again with `role`
     * set, instead of the web flow's session-parked "choose role" redirect.
     */
    public function google(Request $request, FirebaseAuthService $firebaseAuth): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
            'role' => ['sometimes', 'in:organizer,player'],
        ]);

        $claims = $firebaseAuth->verify($request->input('id_token'));

        if (!$claims || empty($claims['email'])) {
            return $this->fail('Google prijava nije uspjela.', 422);
        }

        if (empty($claims['email_verified'])) {
            return $this->fail('Vaš Google email nije verifikovan.', 422);
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

            $token = $user->createToken($this->tokenName($request))->plainTextToken;

            return $this->ok([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'Login successful');
        }

        if (!$request->filled('role')) {
            return $this->ok([
                'needs_role' => true,
                'profile' => ['name' => $name, 'email' => $email, 'avatar' => $avatar],
            ], 'New Google account - role required to finish registration');
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'google_id' => $googleId,
            'avatar' => $avatar,
        ]);
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

        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Logged out successfully');
    }

    /**
     * Revoke every token belonging to the user - "log out of all devices".
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->ok(null, 'Logged out of all devices');
    }

    /**
     * List the user's active API tokens/devices (never exposes the token
     * value itself, only metadata) so the app can show a "manage devices"
     * screen and let the user revoke one individually if needed.
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->orderByDesc('last_used_at')->get()
            ->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'is_current' => $token->id === $request->user()->currentAccessToken()?->id,
            ]);

        return $this->ok($tokens);
    }

    /**
     * Revoke a single token by id (e.g. "log out this device" from the
     * device list) - must belong to the requesting user.
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $tokenId)->delete();

        if (!$deleted) {
            return $this->fail('Token not found.', 404);
        }

        return $this->ok(null, 'Token revoked');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->ok(new UserResource($request->user()->load('playerProfile')));
    }

    /**
     * Upload (or replace) the authenticated user's avatar image.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:4096'],
        ]);

        $user = $request->user();
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->forceFill(['avatar' => \Illuminate\Support\Facades\Storage::url($path)])->save();

        return $this->ok(new UserResource($user), 'Avatar uploaded successfully');
    }

    /**
     * Send a password-reset link to the given email (if an account exists) -
     * always responds the same way regardless of whether the email matched,
     * to avoid leaking which emails are registered.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        return $this->ok(null, 'Ako nalog sa ovim emailom postoji, poslan je link za resetovanje lozinke.');
    }

    /**
     * Complete a password reset using the token from the emailed link.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->fail(__($status), 422);
        }

        return $this->ok(null, 'Lozinka je uspješno promijenjena. Prijavite se ponovo.');
    }

    /**
     * Resend the email-verification link to the authenticated user.
     */
    public function sendVerificationNotification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->ok(null, 'Email je već verifikovan.');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->ok(null, 'Link za verifikaciju je poslan.');
    }

    private function tokenName(Request $request): string
    {
        return $request->input('device_name') ?: ($request->userAgent() ?? 'api');
    }
}
