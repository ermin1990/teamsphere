<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PlayerResource;
use App\Models\Organization;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $query = $organization->players();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('type')) {
            if ($request->type === 'registered') {
                $query->whereNotNull('user_id');
            } elseif ($request->type === 'named') {
                $query->whereNull('user_id');
            }
        }

        $players = $query->orderBy('name')->get();

        return $this->ok(PlayerResource::collection($players));
    }

    public function show(Organization $organization, Player $player): JsonResponse
    {
        abort_unless($player->organization_id === $organization->id, 404);

        $this->authorize('view', $player->organization);

        return $this->ok(new PlayerResource($player));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:players,email,NULL,id,organization_id,' . $organization->id],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'position' => ['nullable', 'string', 'max:100'],
            'jersey_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (!empty($validated['user_id'])) {
            $existingPlayer = Player::where('organization_id', $organization->id)
                ->where('user_id', $validated['user_id'])
                ->first();

            if ($existingPlayer) {
                return $this->fail('This user is already a player in this organization.', 422);
            }
        }

        $userId = $validated['user_id'] ?? null;
        if (!$userId && !empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        $player = Player::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'user_id' => $userId,
            'organization_id' => $organization->id,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'position' => $validated['position'] ?? null,
            'jersey_number' => $validated['jersey_number'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return $this->created(new PlayerResource($player), 'Player created successfully');
    }

    public function update(Request $request, Organization $organization, Player $player): JsonResponse
    {
        abort_unless($player->organization_id === $organization->id, 404);

        $this->authorize('update', $player->organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('players')->ignore($player->id)->where('organization_id', $organization->id)],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'position' => ['nullable', 'string', 'max:100'],
            'jersey_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (!empty($validated['user_id']) && $validated['user_id'] != $player->user_id) {
            $existingPlayer = Player::where('organization_id', $organization->id)
                ->where('user_id', $validated['user_id'])
                ->where('id', '!=', $player->id)
                ->first();

            if ($existingPlayer) {
                return $this->fail('This user is already a player in this organization.', 422);
            }
        }

        $userId = $validated['user_id'] ?? $player->user_id;
        if (!$userId && array_key_exists('email', $validated) && $validated['email']) {
            $user = User::where('email', $validated['email'])->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        $player->update([
            'name' => $validated['name'] ?? $player->name,
            'email' => array_key_exists('email', $validated) ? $validated['email'] : $player->email,
            'user_id' => $userId,
            'date_of_birth' => array_key_exists('date_of_birth', $validated) ? $validated['date_of_birth'] : $player->date_of_birth,
            'position' => array_key_exists('position', $validated) ? $validated['position'] : $player->position,
            'jersey_number' => array_key_exists('jersey_number', $validated) ? $validated['jersey_number'] : $player->jersey_number,
            'is_active' => $validated['is_active'] ?? $player->is_active,
        ]);

        return $this->ok(new PlayerResource($player), 'Player updated successfully');
    }

    public function destroy(Organization $organization, Player $player): JsonResponse
    {
        abort_unless($player->organization_id === $organization->id, 404);

        $this->authorize('update', $player->organization);

        $player->delete();

        return $this->ok(null, 'Player deleted successfully');
    }

    /**
     * List the matches this player has taken part in within the organization.
     * Delegates the query entirely to Player::matchesInOrganization().
     */
    public function matches(Organization $organization, Player $player): JsonResponse
    {
        abort_unless($player->organization_id === $organization->id, 404);

        $this->authorize('view', $player->organization);

        $matches = $player->matchesInOrganization($organization->id);

        return $this->ok($matches);
    }
}
