<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompetitionJoinRequestResource;
use App\Models\Competition;
use App\Models\CompetitionJoinRequest;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompetitionJoinRequestController extends Controller
{
    use ApiResponses;

    /**
     * List join requests for a competition. Organizer-only.
     */
    public function index(Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        $joinRequests = $competition->joinRequests()
            ->with('user')
            ->latest()
            ->get();

        return $this->ok(CompetitionJoinRequestResource::collection($joinRequests));
    }

    /**
     * Submit a request to join a public, individual (non-team-based)
     * competition. Self-serve - any authenticated user, no organization
     * ownership required. Mirrors PlayerLeagueController::store.
     */
    public function store(Request $request, Competition $competition): JsonResponse
    {
        if (!$competition->registration_open) {
            return $this->fail('Ovo takmičenje nije otvoreno za prijave.', 422);
        }

        if ($competition->is_team_based) {
            return $this->fail('Prijava je dostupna samo za pojedinačna takmičenja - za timska takmičenja kontaktiraj organizatora.', 422);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string'],
        ]);

        $userId = $request->user()->id;

        if ($competition->players()->where('players.user_id', $userId)->exists()) {
            return $this->fail('Već si prijavljen na ovo takmičenje.', 422);
        }

        if (CompetitionJoinRequest::where('competition_id', $competition->id)->where('user_id', $userId)->where('status', 'pending')->exists()) {
            return $this->fail('Već imaš zahtjev na čekanju za ovo takmičenje.', 422);
        }

        $joinRequest = CompetitionJoinRequest::create([
            'competition_id' => $competition->id,
            'user_id' => $userId,
            'status' => 'pending',
            'message' => $validated['message'] ?? null,
        ]);

        return $this->created(new CompetitionJoinRequestResource($joinRequest), 'Zahtjev za pridruživanje je poslan organizatoru.');
    }

    /**
     * Approve or reject a join request. Organizer-only. On approval, claims
     * (or creates) a Player row for the requesting user and attaches them
     * to the competition, mirroring CompetitionJoinRequestController::approve.
     */
    public function update(Request $request, Competition $competition, CompetitionJoinRequest $joinRequest): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        abort_unless($joinRequest->competition_id === $competition->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        if (!$joinRequest->isPending()) {
            return $this->fail('Ovaj zahtjev je već obrađen.', 422);
        }

        if ($validated['status'] === 'approved') {
            if ($competition->isTournament() && $competition->max_participants) {
                if ($competition->players()->count() >= $competition->max_participants) {
                    return $this->fail('Dostignut je maksimalan broj učesnika.', 422);
                }
            }

            $organization = $competition->organization;
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
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
            ]);
        } else {
            $joinRequest->update([
                'status' => 'rejected',
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
            ]);
        }

        return $this->ok(new CompetitionJoinRequestResource($joinRequest->fresh(['user'])), 'Zahtjev je obrađen.');
    }
}
