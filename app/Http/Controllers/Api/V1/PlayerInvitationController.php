<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PlayerInvitationResource;
use App\Mail\PlayerInvited;
use App\Models\Competition;
use App\Models\Organization;
use App\Models\Player;
use App\Models\PlayerInvitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlayerInvitationController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $query = PlayerInvitation::where('organization_id', $organization->id);

        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->integer('competition_id'));
        }

        $invitations = $query->orderByDesc('created_at')->get();

        return $this->ok(PlayerInvitationResource::collection($invitations));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'competition_id' => ['required', 'integer', Rule::exists('competitions', 'id')->where('organization_id', $organization->id)],
            'player_id' => ['nullable', 'integer', Rule::exists('players', 'id')->where('organization_id', $organization->id)],
            'name' => ['required_without:player_id', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $competition = Competition::findOrFail($validated['competition_id']);

        if (!empty($validated['player_id'])) {
            $player = Player::findOrFail($validated['player_id']);
        } else {
            $player = Player::firstOrCreate(
                ['organization_id' => $organization->id, 'email' => $validated['email']],
                ['name' => $validated['name']]
            );
        }

        if (!$competition->players()->where('players.id', $player->id)->exists()) {
            $competition->players()->attach($player->id, ['joined_at' => now()]);
        }

        if ($player->isRegistered()) {
            return $this->ok(null, 'Player is already registered and has been added to the competition.');
        }

        $invitation = PlayerInvitation::create([
            'competition_id' => $competition->id,
            'organization_id' => $organization->id,
            'player_id' => $player->id,
            'email' => $validated['email'],
            'token' => Str::random(40),
            'invited_by' => $request->user()->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($invitation->email)->send(new PlayerInvited($invitation));
        } catch (\Exception $e) {
            Log::error('Failed to send player invitation email', ['error' => $e->getMessage()]);
        }

        return $this->created(new PlayerInvitationResource($invitation), 'Invitation sent successfully');
    }

    public function destroy(Organization $organization, PlayerInvitation $playerInvitation): JsonResponse
    {
        abort_unless($playerInvitation->organization_id === $organization->id, 404);

        $this->authorize('update', $organization);

        if ($playerInvitation->status !== 'pending') {
            return $this->fail('Only pending invitations can be cancelled.', 422);
        }

        $playerInvitation->delete();

        return $this->ok(null, 'Invitation cancelled successfully');
    }
}
