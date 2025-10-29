<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    /**
     * Display a listing of players for the organization.
     */
    public function index(Organization $organization)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);

        $players = $organization->players()->active()->get();

        return view('organizations.players.index', compact('organization', 'players'));
    }

    /**
     * Show the form for creating a new player.
     */
    public function create(Organization $organization)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);

        return view('organizations.players.create', compact('organization'));
    }

    /**
     * Store a newly created player.
     */
    public function store(Request $request, Organization $organization)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:players,email,NULL,id,organization_id,' . $organization->id],
            'user_id' => ['nullable', 'exists:users,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'position' => ['nullable', 'string', 'max:100'],
            'jersey_number' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        // If user_id is provided, check if that user is already a player in this organization
        if ($request->user_id) {
            $existingPlayer = Player::where('organization_id', $organization->id)
                ->where('user_id', $request->user_id)
                ->first();

            if ($existingPlayer) {
                return back()->withErrors(['user_id' => 'This user is already a player in this organization.']);
            }
        }

        // If email is provided and no user_id, check if email matches a registered user
        $userId = $request->user_id;
        if (!$userId && $request->email) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        Player::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => $userId,
            'organization_id' => $organization->id,
            'date_of_birth' => $request->date_of_birth,
            'position' => $request->position,
            'jersey_number' => $request->jersey_number,
            'is_active' => true,
        ]);

        return redirect()->route('organizations.players.index', $organization)->with('success', __('Player added successfully!'));
    }

    /**
     * Display the specified player.
     */
    public function show(Organization $organization, Player $player)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);
        
        // Ensure player belongs to organization
        if ($player->organization_id !== $organization->id) {
            abort(404);
        }

        // Učitaj sve odigrane mečeve u kojima je igrač učestvovao u ovoj organizaciji
        $matches = $player->matchesInOrganization($organization->id);

        return view('organizations.players.show', compact('organization', 'player', 'matches'));
    }

    /**
     * Show the form for editing the specified player.
     */
    public function edit(Organization $organization, Player $player)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);
        
        // Ensure player belongs to organization
        if ($player->organization_id !== $organization->id) {
            abort(404);
        }

        return view('organizations.players.edit', compact('organization', 'player'));
    }

    /**
     * Update the specified player.
     */
    public function update(Request $request, Organization $organization, Player $player)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);
        
        // Ensure player belongs to organization
        if ($player->organization_id !== $organization->id) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('players')->ignore($player->id)->where('organization_id', $organization->id)],
            'user_id' => ['nullable', 'exists:users,id'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'position' => ['nullable', 'string', 'max:100'],
            'jersey_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'is_active' => ['boolean'],
        ]);

        // If user_id is provided, check if that user is already a player in this organization (excluding current player)
        if ($request->user_id && $request->user_id != $player->user_id) {
            $existingPlayer = Player::where('organization_id', $organization->id)
                ->where('user_id', $request->user_id)
                ->where('id', '!=', $player->id)
                ->first();

            if ($existingPlayer) {
                return back()->withErrors(['user_id' => 'This user is already a player in this organization.']);
            }
        }

        // If email is provided and no user_id, check if email matches a registered user
        $userId = $request->user_id ?: $player->user_id;
        if (!$userId && $request->email) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        $player->update([
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => $userId,
            'date_of_birth' => $request->date_of_birth,
            'position' => $request->position,
            'jersey_number' => $request->jersey_number,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('organizations.players.show', [$organization, $player])->with('success', __('Player updated successfully!'));
    }

    /**
     * Remove the specified player.
     */
    public function destroy(Organization $organization, Player $player)
    {
        // Use policy for authorization
        $this->authorize('update', $organization);
        
        // Ensure player belongs to organization
        if ($player->organization_id !== $organization->id) {
            abort(404);
        }

        $player->delete();

        return redirect()->route('organizations.players.index', $organization)->with('success', __('Player deleted successfully!'));
    }
}
