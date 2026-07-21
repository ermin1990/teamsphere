<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectorController;
use App\Http\Controllers\SemaforController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bs'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale');

Route::get('/', function () {
    $organizationsCount = \App\Models\Organization::count();
    $activeCompetitionsCount = \App\Models\Competition::where('is_public', true)
        ->whereIn('status', ['active', 'in_progress'])
        ->count();
    $playersCount = \App\Models\Player::count();
    $matchesPlayedCount = \App\Models\LeagueMatch::where('status', 'completed')->count();

    return view('welcome', compact(
        'organizationsCount',
        'activeCompetitionsCount',
        'playersCount',
        'matchesPlayedCount'
    ));
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Display routes (Live Matches Display Screen)
Route::get('/live', [DisplayController::class, 'selector'])->name('display.selector'); // Public league selector
Route::get('/display', [DisplayController::class, 'show'])->name('display.show'); // Public display screen
Route::middleware(['auth'])->group(function () {
    Route::get('/display/admin', [DisplayController::class, 'admin'])->name('display.admin');
    Route::post('/display/toggle/{league}', [DisplayController::class, 'toggleLeague'])->name('display.toggle');
});

// Feedback routes
Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
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

    // Cities admin
    Route::get('/cities', [\App\Http\Controllers\Admin\CityController::class, 'index'])->name('cities.index');
    Route::post('/cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
    Route::put('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.destroy');

    // Venues admin
    Route::get('/venues', [\App\Http\Controllers\Admin\VenueController::class, 'index'])->name('venues.index');
    Route::post('/venues', [\App\Http\Controllers\Admin\VenueController::class, 'store'])->name('venues.store');
    Route::put('/venues/{venue}', [\App\Http\Controllers\Admin\VenueController::class, 'update'])->name('venues.update');
    Route::delete('/venues/{venue}', [\App\Http\Controllers\Admin\VenueController::class, 'destroy'])->name('venues.destroy');

    // Users admin
    Route::get('/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\AdminUserController::class, 'show'])->name('users.show');

    // Organizations admin
    Route::get('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'show'])->name('organizations.show')->middleware('log.organization');

    // Leagues admin
    Route::get('/leagues', [\App\Http\Controllers\AdminLeagueController::class, 'index'])->name('leagues.index');
    Route::get('/leagues/{league}', [\App\Http\Controllers\AdminLeagueController::class, 'show'])->name('leagues.show');
    Route::post('/leagues/{league}/close', [\App\Http\Controllers\AdminLeagueController::class, 'close'])->name('leagues.close');
    Route::post('/leagues/bulk-close', [\App\Http\Controllers\AdminLeagueController::class, 'bulkClose'])->name('leagues.bulk-close');

    // Plans admin
    Route::get('/plans', [\App\Http\Controllers\AdminPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}', [\App\Http\Controllers\AdminPlanController::class, 'show'])->name('plans.show');
    Route::get('/plans/{plan}/edit', [\App\Http\Controllers\AdminPlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [\App\Http\Controllers\AdminPlanController::class, 'update'])->name('plans.update');

    // Plan assignment
    Route::get('/users/{user}/assign-plan', [\App\Http\Controllers\AdminPlanController::class, 'assign'])->name('users.assign-plan');
    Route::post('/users/{user}/assign-plan', [\App\Http\Controllers\AdminPlanController::class, 'assignStore'])->name('users.assign-plan.store');

    // Settings admin
    Route::get('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/plan-upgrade-request', [\App\Http\Controllers\PlanUpgradeController::class, 'store'])->name('plan-upgrade.request');
    Route::get('/moj-plan', [\App\Http\Controllers\PlanController::class, 'show'])->name('plan.show');

    // Player dashboard + invitations
    Route::get('/moje-lige', [\App\Http\Controllers\PlayerDashboardController::class, 'dashboard'])->name('player.dashboard');
    Route::get('/moje-lige/mecevi', [\App\Http\Controllers\PlayerDashboardController::class, 'matches'])->name('player.dashboard.matches');
    Route::get('/moje-lige/takmicenja', [\App\Http\Controllers\PlayerLeagueController::class, 'index'])->name('player.leagues.index');
    Route::post('/moje-lige/takmicenja/{competition}/prijava', [\App\Http\Controllers\PlayerLeagueController::class, 'store'])->name('player.leagues.apply');
    Route::get('/moje-lige/takmicenja/{competition}', [\App\Http\Controllers\PlayerLeagueController::class, 'show'])->name('player.leagues.show');
    Route::get('/moje-lige/{competition}/mecevi/novi', [\App\Http\Controllers\PlayerMatchController::class, 'create'])->name('player.matches.create');
    Route::post('/moje-lige/{competition}/mecevi', [\App\Http\Controllers\PlayerMatchController::class, 'store'])->name('player.matches.store');
    Route::get('/moje-lige/mecevi/{match}/uzivo', [\App\Http\Controllers\PlayerMatchController::class, 'liveScore'])->name('player.matches.live');
    Route::get('/moje-lige/mecevi/{match}/rezultat', [\App\Http\Controllers\PlayerMatchController::class, 'editResult'])->name('player.matches.result.edit');
    Route::put('/moje-lige/mecevi/{match}/rezultat', [\App\Http\Controllers\PlayerMatchController::class, 'updateResult'])->name('player.matches.result.update');
    Route::post('/moje-lige/mecevi/{match}/reset', [\App\Http\Controllers\PlayerMatchController::class, 'resetResult'])->name('player.matches.result.reset');
    Route::get('/pozivnica/{token}', [\App\Http\Controllers\PlayerInvitationController::class, 'accept'])->name('player-invitations.accept');
    Route::post('organizations/{organization}/competitions/{competition}/invite-player', [\App\Http\Controllers\PlayerInvitationController::class, 'store'])->name('organizations.competitions.invite-player');
    Route::post('organizations/{organization}/competitions/{competition}/join-requests/{joinRequest}/approve', [\App\Http\Controllers\CompetitionJoinRequestController::class, 'approve'])->name('organizations.competitions.join-requests.approve');
    Route::post('organizations/{organization}/competitions/{competition}/join-requests/{joinRequest}/reject', [\App\Http\Controllers\CompetitionJoinRequestController::class, 'reject'])->name('organizations.competitions.join-requests.reject');

    // Organization routes
    Route::resource('organizations', OrganizationController::class);
    Route::get('organizations/{organization}/links', [App\Http\Controllers\OrganizationLinkController::class, 'index'])->name('organizations.links.index');
    Route::post('organizations/{organization}/links', [App\Http\Controllers\OrganizationLinkController::class, 'store'])->name('organizations.links.store');
    Route::get('organizations/{organization}/links/{link}/delete', [App\Http\Controllers\OrganizationLinkController::class, 'destroy'])->name('organizations.links.destroy');

    // Season routes
    Route::get('organizations/{organization}/seasons', [App\Http\Controllers\SeasonController::class, 'index'])->name('organizations.seasons.index');
    Route::post('organizations/{organization}/seasons', [App\Http\Controllers\SeasonController::class, 'store'])->name('organizations.seasons.store');
    Route::put('organizations/{organization}/seasons/{season}', [App\Http\Controllers\SeasonController::class, 'update'])->name('organizations.seasons.update');
    Route::delete('organizations/{organization}/seasons/{season}', [App\Http\Controllers\SeasonController::class, 'destroy'])->name('organizations.seasons.destroy');


    // Team routes
    Route::get('organizations/{organization}/teams', [TeamController::class, 'index'])->name('organizations.teams.index');
    Route::resource('organizations.teams', TeamController::class)->except(['index']);
    Route::get('organizations/{organization}/teams/{team}/roster', [TeamController::class, 'roster'])->name('organizations.teams.roster');
    Route::post('organizations/{organization}/teams/{team}/roster', [TeamController::class, 'addPlayer'])->name('organizations.teams.roster.add');
    Route::post('organizations/{organization}/teams/{team}/roster/bulk', [TeamController::class, 'bulkAddPlayers'])->name('organizations.teams.roster.bulk-add');
    Route::delete('organizations/{organization}/teams/{team}/roster/{player}', [TeamController::class, 'removePlayer'])->name('organizations.teams.roster.remove');
    
    // Coach management
    Route::post('organizations/{organization}/teams/{team}/coaches', [TeamController::class, 'addCoach'])->name('organizations.teams.coaches.add');
    Route::post('organizations/{organization}/teams/{team}/coaches/{coach}/toggle', [TeamController::class, 'toggleCoachStatus'])->name('organizations.teams.coaches.toggle');
    Route::delete('organizations/{organization}/teams/{team}/coaches/{coach}', [TeamController::class, 'removeCoach'])->name('organizations.teams.coaches.remove');

    Route::post('organizations/{organization}/teams/suggest', [TeamController::class, 'suggestTeams'])->name('organizations.teams.suggest');

    // Team Matches
    Route::get('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}', [\App\Http\Controllers\TeamMatchController::class, 'show'])
        ->name('organizations.competitions.team-matches.show');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches', [\App\Http\Controllers\TeamMatchController::class, 'store'])
        ->name('organizations.competitions.team-matches.store');
    Route::get('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/protocol', [\App\Http\Controllers\TeamMatchController::class, 'protocol'])
        ->name('organizations.competitions.team-matches.protocol');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/protocol', [\App\Http\Controllers\TeamMatchController::class, 'storeProtocol'])
        ->name('organizations.competitions.team-matches.store-protocol');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/initialize', [\App\Http\Controllers\TeamMatchController::class, 'initializeIndividualMatches'])
        ->name('organizations.competitions.team-matches.initialize');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/add-single', [\App\Http\Controllers\TeamMatchController::class, 'addSingleMatch'])
        ->name('organizations.competitions.team-matches.add-single');
    Route::delete('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/individual/{match}', [\App\Http\Controllers\TeamMatchController::class, 'destroyIndividualMatch'])
        ->name('organizations.competitions.team-matches.individual.destroy');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/individual/{match}/players', [\App\Http\Controllers\TeamMatchController::class, 'updateIndividualPlayers'])
        ->name('organizations.competitions.team-matches.individual.players');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/lineup', [\App\Http\Controllers\TeamMatchController::class, 'updateLineup'])
        ->name('organizations.competitions.team-matches.lineup.update');
    Route::post('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}/captains-referee', [\App\Http\Controllers\TeamMatchController::class, 'updateCaptainsAndReferee'])
        ->name('organizations.competitions.team-matches.captains-referee.update');
    Route::delete('organizations/{organization}/competitions/{competition}/team-matches/{teamMatch}', [\App\Http\Controllers\TeamMatchController::class, 'destroy'])
        ->name('organizations.competitions.team-matches.destroy');

    // Category routes (nested under organizations)
    Route::resource('organizations.categories', CategoryController::class)->shallow();

    // Organization users routes
    Route::get('organizations/{organization}/users', [\App\Http\Controllers\OrganizationUserController::class, 'index'])->name('organizations.users.index');
    Route::get('organizations/{organization}/users/create', [\App\Http\Controllers\OrganizationUserController::class, 'create'])->name('organizations.users.create');
    Route::post('organizations/{organization}/users', [\App\Http\Controllers\OrganizationUserController::class, 'store'])->name('organizations.users.store');
    Route::delete('organizations/{organization}/users/{organizationUser}', [\App\Http\Controllers\OrganizationUserController::class, 'destroy'])->name('organizations.users.destroy');

    // Tables routes (nested under organizations)
    Route::get('organizations/{organization:slug}/tables', [\App\Http\Controllers\TableController::class, 'index'])->name('organizations.tables.index');
    Route::get('organizations/{organization:slug}/tables/schedule', [\App\Http\Controllers\TableController::class, 'schedule'])->name('organizations.tables.schedule');
    Route::get('organizations/{organization:slug}/tables/create', [\App\Http\Controllers\TableController::class, 'create'])->name('organizations.tables.create');
    Route::post('organizations/{organization:slug}/tables', [\App\Http\Controllers\TableController::class, 'store'])->name('organizations.tables.store');
    Route::get('organizations/{organization:slug}/tables/{table}/edit', [\App\Http\Controllers\TableController::class, 'edit'])->name('organizations.tables.edit');
    Route::put('organizations/{organization:slug}/tables/{table}', [\App\Http\Controllers\TableController::class, 'update'])->name('organizations.tables.update');
    Route::delete('organizations/{organization:slug}/tables/{table}', [\App\Http\Controllers\TableController::class, 'destroy'])->name('organizations.tables.destroy');

    // Friendly matches routes (nested under organizations)
    Route::get('organizations/{organization}/friendly-matches', [OrganizationController::class, 'friendlyMatches'])->name('organizations.friendly-matches.index');
    Route::get('organizations/{organization}/friendly-matches/table-tennis', [OrganizationController::class, 'tableTennisFriendly'])->name('organizations.friendly-matches.table-tennis');
    Route::get('organizations/{organization}/friendly-matches/{match}', [OrganizationController::class, 'showFriendlyMatch'])->name('organizations.friendly-matches.show');

    // Player routes (nested under organizations)
    Route::delete('organizations/{organization}/players/bulk-delete', [PlayerController::class, 'bulkDelete'])->name('organizations.players.bulk-delete');
    Route::post('organizations/{organization}/players/bulk-store', [PlayerController::class, 'bulkStore'])->name('organizations.players.bulk-store');
    Route::resource('organizations.players', PlayerController::class)->shallow();

    // Player details route (override the resource show route)
    Route::get('organizations/{organization}/players/{player}', [PlayerController::class, 'show'])->name('organizations.players.show');

    Route::post('organizations/{organization}/competitions/{competition}/matches', [CompetitionController::class, 'storeMatch'])->name('organizations.competitions.matches.store');
    Route::get('organizations/{organization}/competitions/{competition}/matches/{match}', [CompetitionController::class, 'showMatch'])->name('organizations.competitions.matches.show');
    Route::get('organizations/{organization}/competitions/{competition}/matches/{match}/edit', [CompetitionController::class, 'editMatch'])->name('organizations.competitions.matches.edit');
    Route::put('organizations/{organization}/competitions/{competition}/matches/{match}', [CompetitionController::class, 'updateMatch'])->name('organizations.competitions.matches.update');
    Route::delete('organizations/{organization}/competitions/{competition}/matches/{match}', [CompetitionController::class, 'destroyMatch'])->name('organizations.competitions.matches.destroy');

    // Competition routes (nested under organizations)
    Route::get('organizations/{organization}/competitions/create', [CompetitionController::class, 'create'])->name('organizations.competitions.create');
    Route::post('organizations/{organization}/competitions/ai-suggest', [CompetitionController::class, 'aiSuggestCompetition'])
        ->middleware('throttle:ai-league-suggest')
        ->name('organizations.competitions.ai-suggest');
    Route::post('organizations/{organization}/competitions', [CompetitionController::class, 'store'])->name('organizations.competitions.store');
    Route::get('organizations/{organization}/competitions/{competition}', [CompetitionController::class, 'show'])->name('organizations.competitions.show');
    Route::patch('organizations/{organization}/competitions/{competition}', [CompetitionController::class, 'update'])->name('organizations.competitions.update');
    Route::post('organizations/{organization}/competitions/{competition}/add-player', [CompetitionController::class, 'addPlayer'])->name('organizations.competitions.add-player');
    Route::get('organizations/{organization}/competitions/{competition}/bulk-import', [CompetitionController::class, 'showBulkImport'])->name('organizations.competitions.bulk-import');
    Route::post('organizations/{organization}/competitions/{competition}/bulk-import-players', [CompetitionController::class, 'bulkImportPlayers'])->name('organizations.competitions.bulk-import-players');
    Route::delete('organizations/{organization}/competitions/{competition}/players/{player}', [CompetitionController::class, 'removePlayer'])->name('organizations.competitions.remove-player');
    Route::get('organizations/{organization}/competitions/{competition}/manage-players', [CompetitionController::class, 'managePlayers'])->name('organizations.competitions.manage-players');
    Route::get('organizations/{organization}/competitions/{competition}/setup-groups', [CompetitionController::class, 'setupGroups'])->name('organizations.competitions.setup-groups');
    Route::post('organizations/{organization}/competitions/{competition}/save-groups', [CompetitionController::class, 'saveGroups'])->name('organizations.competitions.save-groups');
    Route::get('organizations/{organization}/competitions/{competition}/settings', [CompetitionController::class, 'showSettings'])->name('organizations.competitions.settings');
    Route::post('organizations/{organization}/competitions/{competition}/settings', [CompetitionController::class, 'updateSettings'])->name('organizations.competitions.update-settings');
    Route::post('organizations/{organization}/competitions/{competition}/start', [CompetitionController::class, 'startCompetition'])->name('organizations.competitions.start');
    Route::post('organizations/{organization}/competitions/{competition}/generate-groups', [CompetitionController::class, 'generateGroups'])->name('organizations.competitions.generate-groups');
    Route::post('organizations/{organization}/competitions/{competition}/groups/{group}/advance', [CompetitionController::class, 'advanceGroupPlayers'])->name('organizations.competitions.groups.advance');
    Route::post('organizations/{organization}/competitions/{competition}/complete', [CompetitionController::class, 'completeTournament'])->name('organizations.competitions.complete');
    Route::post('organizations/{organization}/competitions/{competition}/reset', [CompetitionController::class, 'reset'])->name('organizations.competitions.reset');
    Route::post('organizations/{organization}/competitions/{competition}/update-match-players', [CompetitionController::class, 'updateMatchPlayers'])->name('organizations.competitions.update-match-players');
    Route::delete('organizations/{organization}/competitions/{competition}', [CompetitionController::class, 'destroy'])->name('organizations.competitions.destroy');

    // Knockout phase routes
    Route::get('organizations/{organization}/competitions/{competition}/knockout-setup', [CompetitionController::class, 'manualKnockoutSetup'])->name('organizations.competitions.knockout-setup');
    Route::post('organizations/{organization}/competitions/{competition}/auto-generate-knockout', [CompetitionController::class, 'autoGenerateKnockout'])->name('organizations.competitions.auto-generate-knockout');
    Route::post('organizations/{organization}/competitions/{competition}/save-manual-knockout', [CompetitionController::class, 'saveManualKnockout'])->name('organizations.competitions.save-manual-knockout');
    Route::post('organizations/{organization}/competitions/{competition}/advance-knockout-round', [CompetitionController::class, 'advanceKnockoutRound'])->name('organizations.competitions.advance-knockout-round');
    Route::post('organizations/{organization}/competitions/{competition}/reset-knockout', [CompetitionController::class, 'resetKnockout'])->name('organizations.competitions.reset-knockout');
    Route::post('organizations/{organization}/competitions/{competition}/reset-groups', [CompetitionController::class, 'resetGroups'])->name('organizations.competitions.reset-groups');

    // Manual standings adjustment
    Route::get('organizations/{organization}/competitions/{competition}/groups/{group}/manual-standings', function(\App\Models\Organization $organization, \App\Models\Competition $competition, \App\Models\TournamentGroup $group) {
        return view('organizations.competitions.manual-standings', compact('organization', 'competition', 'group'));
    })->name('organizations.competitions.groups.manual-standings');
    Route::get('organizations/{organization}/competitions/{competition}/manual-standings', function(\App\Models\Organization $organization, \App\Models\Competition $competition) {
        return view('organizations.competitions.league-manual-standings', compact('organization', 'competition'));
    })->name('organizations.competitions.manual-standings');
    Route::get('leagues/create/{organization}', [LeagueController::class, 'create'])->name('leagues.create');
    Route::get('leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
    Route::get('leagues/{league}/team-management', [LeagueController::class, 'teamManagement'])->name('leagues.team-management');
    Route::post('leagues/{organization}', [LeagueController::class, 'store'])->name('leagues.store');
    Route::put('leagues/{league}', [LeagueController::class, 'update'])->name('leagues.update');
    Route::patch('leagues/{league}', [LeagueController::class, 'update']);
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
    
    Route::get('competitions/matches/{match}/live-score', function(\App\Models\CompetitionMatch $match) {
        $match->load([
            'competition.organization',
            'league.organization',
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer'
        ]);
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
    
    // Competition referee routes
    Route::get('/competitions/{competition}/matches/{match}', [App\Http\Controllers\RefereeController::class, 'showCompetitionMatch'])->name('competition.match.show');
    Route::get('/competitions/{competition}/matches/{match}/edit', [App\Http\Controllers\RefereeController::class, 'editCompetitionMatch'])->name('competition.match.edit');
    Route::get('/competitions/{competition}/matches/{match}/live', [App\Http\Controllers\RefereeController::class, 'liveCompetitionScore'])->name('competition.match.live');
    Route::put('/competitions/{competition}/matches/{match}', [App\Http\Controllers\RefereeController::class, 'updateCompetitionMatch'])->name('competition.match.update');
    Route::patch('/competitions/{competition}/matches/{match}/reset', [App\Http\Controllers\RefereeController::class, 'resetCompetitionMatch'])->name('competition.match.reset');
    
    // Route za startanje lige je sada PATCH u glavnoj LeagueController grupi
});

// Referee routes
Route::middleware(['auth'])->prefix('referee')->name('referee.')->group(function () {
});

// Projector routes (no authentication required)
Route::prefix('projector')->name('projector.')->group(function () {
    Route::get('/builder', [App\Http\Controllers\ProjectorController::class, 'builder'])->name('builder');
    Route::get('/display', [App\Http\Controllers\ProjectorController::class, 'display'])->name('display');
    Route::get('/competition/{competition}', [App\Http\Controllers\ProjectorController::class, 'getCompetitionView'])->name('competition.view');
});

// Public routes (no authentication required)
// URIs are Bosnian/user-facing; route names stay English per Laravel convention.
Route::name('competitions.')->group(function () {
    Route::get('/takmicenja', [App\Http\Controllers\PublicMatchController::class, 'indexLeagues'])->name('index');
    Route::get('/organizacija/{organization}', [App\Http\Controllers\PublicMatchController::class, 'indexLeaguesByOrganization'])->name('organization');
    Route::get('/grad/{city}', [App\Http\Controllers\PublicMatchController::class, 'indexLeaguesByCity'])->name('by-city');
    Route::get('/takmicenja/{competition}', [App\Http\Controllers\PublicMatchController::class, 'showLeague'])->name('show');
    Route::get('/takmicenja/{competition}/semafor', [App\Http\Controllers\PublicMatchController::class, 'competitionSemafor'])->name('semafor');
    Route::get('/igrac/{player}', [App\Http\Controllers\PublicPlayerController::class, 'show'])->name('player.show');

    Route::get('/takmicenja/{competition}/mecevi/{match}', [App\Http\Controllers\PublicMatchController::class, 'showMatch'])->name('matches.show');
    Route::get('/takmicenja/{competition}/ekipni-mecevi/{teamMatch}', [App\Http\Controllers\PublicMatchController::class, 'showTeamMatch'])->name('team-matches.show');
    Route::get('/takmicenja/{competition}/mecevi/{match}/uzivo', [App\Http\Controllers\PublicMatchController::class, 'liveScore'])->name('matches.live');
});

// Public team/club profile
Route::get('/tim/{team}', [App\Http\Controllers\PublicMatchController::class, 'showTeam'])->name('teams.show');
Route::get('/tim/{team}/takmicenja/{competition}/mecevi', [App\Http\Controllers\PublicMatchController::class, 'showTeamCompetitionMatches'])->name('teams.competition-matches');

// AJAX/embed endpoints for the public pages above
Route::get('/api/uzivo-mecevi', [App\Http\Controllers\PublicMatchController::class, 'getLiveMatchesData'])->name('api.live-matches');
Route::get('/api/mecevi/{matchId}', [App\Http\Controllers\PublicMatchController::class, 'getMatchData'])->name('api.match');
Route::get('/embed/mec/{match}', [App\Http\Controllers\PublicMatchController::class, 'embedMatch'])->name('embed.match');

require __DIR__.'/auth.php';
