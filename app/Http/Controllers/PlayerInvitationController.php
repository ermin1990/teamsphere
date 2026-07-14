<?php

namespace App\Http\Controllers;

use App\Mail\PlayerInvited;
use App\Models\Competition;
use App\Models\Organization;
use App\Models\Player;
use App\Models\PlayerInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PlayerInvitationController extends Controller
{
    /**
     * Organizer invites a player (by email) to join a competition.
     */
    public function store(Request $request, Organization $organization, Competition $competition)
    {
        Gate::authorize('update', $organization);
        abort_unless($competition->organization_id === $organization->id, 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $player = Player::firstOrCreate(
            ['organization_id' => $organization->id, 'email' => $request->email],
            ['name' => $request->name]
        );

        if (!$competition->players()->where('players.id', $player->id)->exists()) {
            $competition->players()->attach($player->id, ['joined_at' => now()]);
        }

        if ($player->isRegistered()) {
            return back()->with('success', 'Igrač je već registrovan i odmah je dodan na takmičenje.');
        }

        $invitation = PlayerInvitation::create([
            'competition_id' => $competition->id,
            'organization_id' => $organization->id,
            'player_id' => $player->id,
            'email' => $request->email,
            'token' => Str::random(40),
            'invited_by' => auth()->id(),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($invitation->email)->send(new PlayerInvited($invitation));
        } catch (\Exception $e) {
            \Log::error('Failed to send player invitation email', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Pozivnica je poslana na ' . $request->email . '.');
    }

    /**
     * Player accepts an invitation (must be authenticated - guests are
     * redirected to login/register first and land back here automatically
     * via Laravel's intended-url mechanism).
     */
    public function accept(Request $request, string $token)
    {
        $invitation = PlayerInvitation::where('token', $token)->firstOrFail();

        if ($invitation->status === 'accepted') {
            return redirect()->route('player.dashboard')->with('success', 'Pozivnica je već prihvaćena.');
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return redirect()->route('player.dashboard')->with('error', 'Ova pozivnica je istekla. Zatraži novu od organizatora.');
        }

        $user = auth()->user();

        // Guard against a *different* already-authenticated user claiming
        // someone else's invitation - only the account whose email matches
        // the invited email may link the Player record.
        if (strcasecmp($user->email, $invitation->email) !== 0) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->put('url.intended', route('player-invitations.accept', $invitation->token));

            return redirect()->route('login')
                ->with('error', 'Ova pozivnica je poslana na ' . $invitation->email . '. Prijavite se ili registrujte se sa tim emailom da je prihvatite.')
                ->withInput(['email' => $invitation->email]);
        }

        $userId = $user->id;

        // Link this invitation's player row, and any other unclaimed player
        // rows in the same organization with the same email, to this account.
        Player::where('organization_id', $invitation->organization_id)
            ->where('email', $invitation->email)
            ->whereNull('user_id')
            ->update(['user_id' => $userId]);

        $invitation->update(['status' => 'accepted', 'accepted_at' => now()]);

        return redirect()->route('player.dashboard')->with('success', 'Pridružili ste se takmičenju "' . $invitation->competition->name . '"!');
    }
}
