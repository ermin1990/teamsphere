<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\LeagueController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('organizations/{organization}/competitions/{competition}/manage-players', [CompetitionController::class, 'managePlayers'])->name('organizations.competitions.manage-players');
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

    // League routes
    Route::get('leagues/create/{organization}', [LeagueController::class, 'create'])->name('leagues.create');
    Route::get('leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
    Route::get('leagues/{league}/team-management', [LeagueController::class, 'teamManagement'])->name('leagues.team-management');
    Route::post('leagues/{organization}', [LeagueController::class, 'store'])->name('leagues.store');
    Route::put('leagues/{league}', [LeagueController::class, 'update'])->name('leagues.update');
    Route::delete('leagues/{league}', [LeagueController::class, 'destroy'])->name('leagues.destroy');
    Route::get('leagues/settings-form', [LeagueController::class, 'getSettingsForm'])->name('leagues.settings-form');

    // League teams and players routes
    Route::post('leagues/{league}/teams', [LeagueController::class, 'addTeam'])->name('leagues.teams.store');
    Route::put('leagues/{league}/teams/{team}', [LeagueController::class, 'updateTeam'])->name('leagues.teams.update');
    Route::delete('leagues/{league}/teams/{team}', [LeagueController::class, 'deleteTeam'])->name('leagues.teams.destroy');
    Route::post('leagues/{league}/teams/{team}/add-player', [LeagueController::class, 'addPlayerToTeam'])->name('leagues.teams.add-player');
    Route::delete('leagues/{league}/teams/{team}/remove-player/{player}', [LeagueController::class, 'removePlayerFromTeam'])->name('leagues.teams.remove-player');
    Route::post('leagues/{league}/players', [LeagueController::class, 'addPlayer'])->name('leagues.players.store');
    Route::post('leagues/{league}/add-players', [LeagueController::class, 'addPlayers'])->name('leagues.addPlayers');
    Route::patch('leagues/{league}/start', [LeagueController::class, 'startLeague'])->name('leagues.start');
    Route::post('leagues/{league}/reset', [LeagueController::class, 'resetLeague'])->name('leagues.reset');
    Route::get('leagues/{league}/matches/{match}', [LeagueController::class, 'showMatch'])->name('leagues.matches.show');
    Route::get('leagues/{league}/matches/{match}/edit', [LeagueController::class, 'editMatch'])->name('leagues.matches.edit');
    Route::put('leagues/{league}/matches/{match}', [LeagueController::class, 'updateMatch'])->name('leagues.matches.update');
    Route::get('leagues/{league}/matches/{match}/live', [LeagueController::class, 'liveScore'])->name('leagues.matches.live');
    Route::post('leagues/{league}/matches/{match}/live-score', [LeagueController::class, 'updateLiveScore'])->name('leagues.matches.live-score');
    Route::post('leagues/{league}/matches/{match}/reset', [LeagueController::class, 'resetMatch'])->name('leagues.matches.reset');
    
    // Direct match access routes (for live scoring from competition view)
    Route::get('leagues/matches/{match}/live-score', function($matchId) {
        $match = \App\Models\LeagueMatch::with([
            'league.organization',
            'competition.organization',
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer'
        ])->findOrFail($matchId);
        return view('live-score-page', ['match' => $match]);
    })->name('leagues.live-score');
    
    Route::get('competitions/matches/{match}/live-score', function($matchId) {
        $match = \App\Models\CompetitionMatch::with([
            'competition.organization',
            'league.organization',
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer'
        ])->findOrFail($matchId);
        return view('live-score-page', ['match' => $match]);
    })->name('competitions.live-score');
    
    // Quick result entry routes
    Route::post('leagues/matches/{match}/quick-result', [LeagueController::class, 'quickResult'])->name('leagues.matches.quick-result');
    Route::post('competitions/matches/{match}/quick-result', [CompetitionController::class, 'quickResult'])->name('competitions.matches.quick-result');
});

// Referee routes
Route::middleware(['auth'])->prefix('referee')->name('referee.')->group(function () {
    Route::get('/', [App\Http\Controllers\RefereeController::class, 'dashboard'])->name('dashboard');
    Route::get('/moderator', [App\Http\Controllers\RefereeController::class, 'moderatorDashboard'])->name('moderator.dashboard');
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

// Public routes (no authentication required)
Route::prefix('public')->name('public.')->group(function () {
    // Public league routes
    Route::get('/leagues/{league}', [App\Http\Controllers\PublicMatchController::class, 'showLeague'])->name('leagues.show');

    // Public match routes
    Route::get('/leagues/{league}/matches/{match}', [App\Http\Controllers\PublicMatchController::class, 'showMatch'])->name('matches.show');
    Route::get('/leagues/{league}/matches/{match}/live', [App\Http\Controllers\PublicMatchController::class, 'liveScore'])->name('matches.live');

    // Embed widget
    Route::get('/embed/matches/{match}', [App\Http\Controllers\PublicMatchController::class, 'embedMatch'])->name('matches.embed');
});

require __DIR__.'/auth.php';
