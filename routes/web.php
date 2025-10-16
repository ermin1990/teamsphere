<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bs'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Get players where user is registered
    $players = \App\Models\Player::where('user_id', $user->id)->with('organization', 'homeMatches', 'awayMatches')->get();

    // Get organizations from players
    $playerOrganizations = $players->pluck('organization')->unique();
    
    // Get organizations owned by user
    $ownedOrganizations = $user->organizations()->with(['leagues', 'competitions'])->get();
    
    // Merge both collections and remove duplicates
    $organizations = $playerOrganizations->merge($ownedOrganizations)->unique('id');

    // Get upcoming matches for this player
    $upcomingMatches = collect();
    foreach ($players as $player) {
        // Get matches where player is home or away player
        $playerMatches = \App\Models\LeagueMatch::where(function($query) use ($player) {
            $query->where('home_player_id', $player->id)
                  ->orWhere('away_player_id', $player->id);
        })
        ->with(['league', 'homePlayer', 'awayPlayer'])
        ->where('scheduled_at', '>=', now())
        ->orderBy('scheduled_at', 'asc')
        ->limit(10)
        ->get();

        $upcomingMatches = $upcomingMatches->merge($playerMatches);
    }

    // Check if user is a referee in any organization
    $isReferee = $user->organizationUsers()->where('role', 'referee')->exists();

    return view('dashboard', compact('organizations', 'players', 'upcomingMatches', 'isReferee'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Feedback routes
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/bug-reports', [FeedbackController::class, 'index'])->name('bug-reports.index');
    Route::get('/bug-reports/{bugReport}', [FeedbackController::class, 'show'])->name('bug-reports.show');
    Route::put('/bug-reports/{bugReport}', [FeedbackController::class, 'update'])->name('bug-reports.update');

    // Sports admin
    Route::get('/sports', [\App\Http\Controllers\AdminSportController::class, 'index'])->name('sports.index');
    Route::post('/sports/{sport}/toggle', [\App\Http\Controllers\AdminSportController::class, 'toggle'])->name('sports.toggle');

    // Users admin
    Route::get('/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\AdminUserController::class, 'show'])->name('users.show');

    // Organizations admin
    Route::get('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'show'])->name('organizations.show');

    // Leagues admin
    Route::get('/leagues', [\App\Http\Controllers\AdminLeagueController::class, 'index'])->name('leagues.index');
    Route::get('/leagues/{league}', [\App\Http\Controllers\AdminLeagueController::class, 'show'])->name('leagues.show');

    // Plans admin
    Route::get('/plans', [\App\Http\Controllers\AdminPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}', [\App\Http\Controllers\AdminPlanController::class, 'show'])->name('plans.show');

    // Settings admin
    Route::get('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Organization routes
    Route::resource('organizations', OrganizationController::class);

    // Organization users routes
    Route::get('organizations/{organization}/users', [\App\Http\Controllers\OrganizationUserController::class, 'index'])->name('organizations.users.index');
    Route::get('organizations/{organization}/users/create', [\App\Http\Controllers\OrganizationUserController::class, 'create'])->name('organizations.users.create');
    Route::post('organizations/{organization}/users', [\App\Http\Controllers\OrganizationUserController::class, 'store'])->name('organizations.users.store');
    Route::delete('organizations/{organization}/users/{organizationUser}', [\App\Http\Controllers\OrganizationUserController::class, 'destroy'])->name('organizations.users.destroy');

    // Friendly matches routes (nested under organizations)
    Route::get('organizations/{organization}/friendly-matches', [OrganizationController::class, 'friendlyMatches'])->name('organizations.friendly-matches.index');
    Route::get('organizations/{organization}/friendly-matches/table-tennis', [OrganizationController::class, 'tableTennisFriendly'])->name('organizations.friendly-matches.table-tennis');
    Route::get('organizations/{organization}/friendly-matches/{match}', [OrganizationController::class, 'showFriendlyMatch'])->name('organizations.friendly-matches.show');

    // Player routes (nested under organizations)
    Route::resource('organizations.players', PlayerController::class)->shallow();

    // Player details route (override the resource show route)
    Route::get('organizations/{organization}/players/{player}', [PlayerController::class, 'show'])->name('organizations.players.show');

    // Competition routes (nested under organizations)
    Route::get('organizations/{organization}/competitions/create', [CompetitionController::class, 'create'])->name('organizations.competitions.create');
    Route::post('organizations/{organization}/competitions', [CompetitionController::class, 'store'])->name('organizations.competitions.store');
    Route::get('organizations/{organization}/competitions/{competition}', [CompetitionController::class, 'show'])->name('organizations.competitions.show');
    Route::post('organizations/{organization}/competitions/{competition}/add-player', [CompetitionController::class, 'addPlayer'])->name('organizations.competitions.add-player');
    Route::delete('organizations/{organization}/competitions/{competition}/players/{player}', [CompetitionController::class, 'removePlayer'])->name('organizations.competitions.remove-player');
    Route::get('organizations/{organization}/competitions/{competition}/setup-groups', [CompetitionController::class, 'setupGroups'])->name('organizations.competitions.setup-groups');
    Route::post('organizations/{organization}/competitions/{competition}/save-groups', [CompetitionController::class, 'saveGroups'])->name('organizations.competitions.save-groups');
    Route::get('organizations/{organization}/competitions/{competition}/settings', [CompetitionController::class, 'showSettings'])->name('organizations.competitions.settings');
    Route::post('organizations/{organization}/competitions/{competition}/settings', [CompetitionController::class, 'updateSettings'])->name('organizations.competitions.update-settings');
    Route::post('organizations/{organization}/competitions/{competition}/start', [CompetitionController::class, 'startCompetition'])->name('organizations.competitions.start');
    Route::post('organizations/{organization}/competitions/{competition}/generate-groups', [CompetitionController::class, 'generateGroups'])->name('organizations.competitions.generate-groups');
    Route::post('organizations/{organization}/competitions/{competition}/advance-groups', [CompetitionController::class, 'advanceFromGroups'])->name('organizations.competitions.advance-groups');
    Route::post('organizations/{organization}/competitions/{competition}/groups/{group}/advance', [CompetitionController::class, 'advanceGroupPlayers'])->name('organizations.competitions.groups.advance');
    Route::post('organizations/{organization}/competitions/{competition}/complete', [CompetitionController::class, 'completeTournament'])->name('organizations.competitions.complete');
    Route::post('organizations/{organization}/competitions/{competition}/reset', [CompetitionController::class, 'reset'])->name('organizations.competitions.reset');
    Route::post('organizations/{organization}/competitions/{competition}/update-match-players', [CompetitionController::class, 'updateMatchPlayers'])->name('organizations.competitions.update-match-players');
    Route::post('organizations/{organization}/competitions/{competition}/auto-generate-bracket', [CompetitionController::class, 'autoGenerateBracket'])->name('organizations.competitions.auto-generate-bracket');
    Route::post('organizations/{organization}/competitions/{competition}/generate-next-round', [CompetitionController::class, 'generateNextRound'])->name('organizations.competitions.generate-next-round');
    Route::get('organizations/{organization}/competitions/{competition}/available-players', [CompetitionController::class, 'getAvailablePlayers'])->name('organizations.competitions.available-players');
    Route::delete('organizations/{organization}/competitions/{competition}', [CompetitionController::class, 'destroy'])->name('organizations.competitions.destroy');

    // League routes (nested under organizations)
    Route::get('organizations/{organization}/leagues/create', [LeagueController::class, 'create'])->name('organizations.leagues.create');
    Route::get('organizations/{organization}/leagues/{league}', [LeagueController::class, 'show'])->name('organizations.leagues.show');
    Route::get('organizations/{organization}/leagues/{league}/team-management', [LeagueController::class, 'teamManagement'])->name('organizations.leagues.team-management');
    Route::post('organizations/{organization}/leagues', [LeagueController::class, 'store'])->name('organizations.leagues.store');
    Route::put('organizations/{organization}/leagues/{league}', [LeagueController::class, 'update'])->name('organizations.leagues.update');
    Route::delete('organizations/{organization}/leagues/{league}', [LeagueController::class, 'destroy'])->name('organizations.leagues.destroy');
    Route::get('leagues/settings-form', [LeagueController::class, 'getSettingsForm'])->name('leagues.settings-form');

    // League teams and players routes
    Route::post('organizations/{organization}/leagues/{league}/teams', [LeagueController::class, 'addTeam'])->name('organizations.leagues.teams.store');
    Route::put('organizations/{organization}/leagues/{league}/teams/{team}', [LeagueController::class, 'updateTeam'])->name('organizations.leagues.teams.update');
    Route::delete('organizations/{organization}/leagues/{league}/teams/{team}', [LeagueController::class, 'deleteTeam'])->name('organizations.leagues.teams.destroy');
    Route::post('organizations/{organization}/leagues/{league}/teams/{team}/add-player', [LeagueController::class, 'addPlayerToTeam'])->name('organizations.leagues.teams.add-player');
    Route::delete('organizations/{organization}/leagues/{league}/teams/{team}/remove-player/{player}', [LeagueController::class, 'removePlayerFromTeam'])->name('organizations.leagues.teams.remove-player');
    Route::post('organizations/{organization}/leagues/{league}/players', [LeagueController::class, 'addPlayer'])->name('organizations.leagues.players.store');
    Route::post('organizations/{organization}/leagues/{league}/add-players', [LeagueController::class, 'addPlayers'])->name('organizations.leagues.addPlayers');
    Route::patch('organizations/{organization}/leagues/{league}/start', [LeagueController::class, 'startLeague'])->name('organizations.leagues.start');
    Route::post('organizations/{organization}/leagues/{league}/reset', [LeagueController::class, 'resetLeague'])->name('organizations.leagues.reset');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}', [LeagueController::class, 'showMatch'])->name('organizations.leagues.matches.show');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}/edit', [LeagueController::class, 'editMatch'])->name('organizations.leagues.matches.edit');
    Route::put('organizations/{organization}/leagues/{league}/matches/{match}', [LeagueController::class, 'updateMatch'])->name('organizations.leagues.matches.update');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}/live', [LeagueController::class, 'liveScore'])->name('organizations.leagues.matches.live');
    Route::post('organizations/{organization}/leagues/{league}/matches/{match}/live-score', [LeagueController::class, 'updateLiveScore'])->name('organizations.leagues.matches.live-score');
    Route::post('organizations/{organization}/leagues/{league}/matches/{match}/reset', [LeagueController::class, 'resetMatch'])->name('organizations.leagues.matches.reset');
    
    // Direct match access routes (for live scoring from competition view)
    Route::get('leagues/matches/{match}/live-score', function($matchId) {
        $match = \App\Models\LeagueMatch::findOrFail($matchId);
        return view('livewire.live-score', ['match' => $match]);
    })->name('leagues.live-score');
    
    Route::get('competitions/matches/{match}/live-score', function($matchId) {
        $match = \App\Models\CompetitionMatch::findOrFail($matchId);
        return view('livewire.live-score', ['match' => $match]);
    })->name('competitions.live-score');
    
    // Quick result entry routes
    Route::post('leagues/matches/{match}/quick-result', [LeagueController::class, 'quickResult'])->name('leagues.matches.quick-result');
    Route::post('competitions/matches/{match}/quick-result', [CompetitionController::class, 'quickResult'])->name('competitions.matches.quick-result');
});

// Referee routes
Route::middleware(['auth'])->prefix('referee')->name('referee.')->group(function () {
    Route::get('/', [App\Http\Controllers\RefereeController::class, 'dashboard'])->name('dashboard');
    Route::get('/leagues', [App\Http\Controllers\RefereeController::class, 'leagues'])->name('leagues');
    Route::get('/leagues/{league}/matches', [App\Http\Controllers\RefereeController::class, 'leagueMatches'])->name('league.matches');
    Route::get('/leagues/{league}/matches/{match}', [App\Http\Controllers\RefereeController::class, 'showMatch'])->name('match.show');
    Route::get('/leagues/{league}/matches/{match}/edit', [App\Http\Controllers\RefereeController::class, 'editMatch'])->name('match.edit');
    Route::get('/leagues/{league}/matches/{match}/live', [App\Http\Controllers\RefereeController::class, 'liveScore'])->name('match.live');
    Route::put('/leagues/{league}/matches/{match}', [App\Http\Controllers\RefereeController::class, 'updateMatch'])->name('match.update');
    Route::post('/leagues/{league}/matches/{match}/reset', [App\Http\Controllers\RefereeController::class, 'resetMatch'])->name('match.reset');
    // Route za startanje lige je sada PATCH u glavnoj LeagueController grupi
});

// Referee routes
Route::middleware(['auth'])->prefix('referee')->name('referee.')->group(function () {
});

require __DIR__.'/auth.php';
