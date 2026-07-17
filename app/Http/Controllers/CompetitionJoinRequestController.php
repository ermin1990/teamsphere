<?php

namespace App\Http\Controllers;

use App\Mail\CompetitionJoinRequestDecided;
use App\Models\Competition;
use App\Models\CompetitionJoinRequest;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class CompetitionJoinRequestController extends Controller
{
    /**
     * Approve a player's request to join a competition - claims the user's
     * org-less Player row for this organization (or creates one if they
     * already belong to a different organization), then attaches them to
     * the competition. Mirrors the attach pattern in
     * CompetitionController::addPlayer / PlayerInvitationController::store.
     */
    public function approve(Organization $organization, Competition $competition, CompetitionJoinRequest $joinRequest)
    {
        Gate::authorize('update', $organization);
        abort_unless($competition->organization_id === $organization->id, 404);
        abort_unless($joinRequest->competition_id === $competition->id, 404);

        if (!$joinRequest->isPending()) {
            return back()->with('error', 'Ovaj zahtjev je već obrađen.');
        }

        if ($competition->isTournament() && $competition->max_participants) {
            if ($competition->players()->count() >= $competition->max_participants) {
                return back()->with('error', 'Dostignut je maksimalan broj učesnika.');
            }
        }

        $user = $joinRequest->user;

        $player = Player::where('user_id', $user->id)->whereNull('organization_id')->first();
        if ($player) {
            $player->update(['organization_id' => $organization->id]);
        } else {
            $player = Player::firstOrCreate(
                ['user_id' => $user->id, 'organization_id' => $organization->id],
                ['name' => $user->name, 'email' => $user->email]
            );
        }

        if (!$competition->players()->where('players.id', $player->id)->exists()) {
            $competition->players()->attach($player->id, ['joined_at' => now()]);
        }

        $joinRequest->update([
            'status' => 'approved',
            'decided_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        $this->notifyPlayerOfDecision($joinRequest);

        return back()->with('success', $user->name . ' je dodan/a na takmičenje.');
    }

    public function reject(Organization $organization, Competition $competition, CompetitionJoinRequest $joinRequest)
    {
        Gate::authorize('update', $organization);
        abort_unless($competition->organization_id === $organization->id, 404);
        abort_unless($joinRequest->competition_id === $competition->id, 404);

        if (!$joinRequest->isPending()) {
            return back()->with('error', 'Ovaj zahtjev je već obrađen.');
        }

        $joinRequest->update([
            'status' => 'rejected',
            'decided_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        $this->notifyPlayerOfDecision($joinRequest);

        return back()->with('success', 'Zahtjev je odbijen.');
    }

    /**
     * Let the player know their join request was approved/rejected.
     */
    private function notifyPlayerOfDecision(CompetitionJoinRequest $joinRequest): void
    {
        $playerEmail = $joinRequest->user->email ?? null;
        if (!$playerEmail) {
            return;
        }

        try {
            $joinRequest->load(['user', 'competition.organization']);
            Mail::to($playerEmail)->send(new CompetitionJoinRequestDecided($joinRequest));
        } catch (\Exception $e) {
            \Log::error('Failed to send competition join request decision email', ['error' => $e->getMessage()]);
        }
    }
}
