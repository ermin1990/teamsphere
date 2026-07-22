<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\CompetitionJoinRequestController;
use App\Http\Controllers\Api\V1\CompetitionMatchController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\FriendlyMatchController;
use App\Http\Controllers\Api\V1\LeagueController;
use App\Http\Controllers\Api\V1\LeagueMatchController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\OrganizationLinkController;
use App\Http\Controllers\Api\V1\OrganizationUserController;
use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\PlayerInvitationController;
use App\Http\Controllers\Api\V1\SeasonController;
use App\Http\Controllers\Api\V1\SportController;
use App\Http\Controllers\Api\V1\StandingController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\TeamCoachController;
use App\Http\Controllers\Api\V1\TeamMatchController;
use App\Http\Controllers\Api\V1\TournamentGroupController;
use App\Http\Controllers\Api\V1\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Auth (throttled - these are unauthenticated brute-force/enumeration targets)
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/auth/google', [AuthController::class, 'google'])->name('auth.google');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    });

    // Public reference data
    Route::get('/sports', [SportController::class, 'index'])->name('sports.index');
    Route::get('/sports/{sport}', [SportController::class, 'show'])->name('sports.show');
    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/cities/{city}', [CityController::class, 'show'])->name('cities.show');
    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
    Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
        Route::get('/tokens', [AuthController::class, 'tokens'])->name('auth.tokens.index');
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken'])->name('auth.tokens.destroy');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/me/avatar', [AuthController::class, 'uploadAvatar'])->name('auth.avatar');
        Route::get('/me/competitions', [AuthController::class, 'myCompetitions'])->name('auth.competitions');
        Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationNotification'])
            ->middleware('throttle:6,1')->name('auth.verification.send');

        // Push notification device tokens
        Route::post('/device-tokens', [DeviceTokenController::class, 'store'])->name('device-tokens.store');
        Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy'])->name('device-tokens.destroy');

        // Reference data writes (admin-only, enforced in controllers)
        Route::post('/sports', [SportController::class, 'store'])->name('sports.store');
        Route::put('/sports/{sport}', [SportController::class, 'update'])->name('sports.update');
        Route::delete('/sports/{sport}', [SportController::class, 'destroy'])->name('sports.destroy');
        Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
        Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');
        Route::post('/venues', [VenueController::class, 'store'])->name('venues.store');
        Route::put('/venues/{venue}', [VenueController::class, 'update'])->name('venues.update');
        Route::delete('/venues/{venue}', [VenueController::class, 'destroy'])->name('venues.destroy');

        // Organizations
        Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::put('/organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');
        Route::delete('/organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy');
        Route::post('/organizations/{organization}/logo', [OrganizationController::class, 'uploadLogo'])->name('organizations.logo');

        Route::get('/organizations/{organization}/users', [OrganizationUserController::class, 'index'])->name('organizations.users.index');
        Route::post('/organizations/{organization}/users', [OrganizationUserController::class, 'store'])->name('organizations.users.store');
        Route::delete('/organizations/{organization}/users/{organizationUser}', [OrganizationUserController::class, 'destroy'])->name('organizations.users.destroy');

        Route::get('/organizations/{organization}/links', [OrganizationLinkController::class, 'index'])->name('organizations.links.index');
        Route::post('/organizations/{organization}/links', [OrganizationLinkController::class, 'store'])->name('organizations.links.store');
        Route::delete('/organizations/{organization}/links/{link}', [OrganizationLinkController::class, 'destroy'])->name('organizations.links.destroy');

        Route::get('/organizations/{organization}/categories', [CategoryController::class, 'index'])->name('organizations.categories.index');
        Route::post('/organizations/{organization}/categories', [CategoryController::class, 'store'])->name('organizations.categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/organizations/{organization}/seasons', [SeasonController::class, 'index'])->name('organizations.seasons.index');
        Route::post('/organizations/{organization}/seasons', [SeasonController::class, 'store'])->name('organizations.seasons.store');
        Route::get('/organizations/{organization}/seasons/{season}', [SeasonController::class, 'show'])->name('organizations.seasons.show');
        Route::put('/organizations/{organization}/seasons/{season}', [SeasonController::class, 'update'])->name('organizations.seasons.update');
        Route::delete('/organizations/{organization}/seasons/{season}', [SeasonController::class, 'destroy'])->name('organizations.seasons.destroy');

        Route::get('/organizations/{organization}/tables', [TableController::class, 'index'])->name('organizations.tables.index');
        Route::post('/organizations/{organization}/tables', [TableController::class, 'store'])->name('organizations.tables.store');
        Route::get('/organizations/{organization}/tables/{table}', [TableController::class, 'show'])->name('organizations.tables.show');
        Route::put('/organizations/{organization}/tables/{table}', [TableController::class, 'update'])->name('organizations.tables.update');
        Route::delete('/organizations/{organization}/tables/{table}', [TableController::class, 'destroy'])->name('organizations.tables.destroy');

        Route::get('/organizations/{organization}/players', [PlayerController::class, 'index'])->name('organizations.players.index');
        Route::post('/organizations/{organization}/players', [PlayerController::class, 'store'])->name('organizations.players.store');
        Route::get('/organizations/{organization}/players/{player}', [PlayerController::class, 'show'])->name('organizations.players.show');
        Route::put('/organizations/{organization}/players/{player}', [PlayerController::class, 'update'])->name('organizations.players.update');
        Route::delete('/organizations/{organization}/players/{player}', [PlayerController::class, 'destroy'])->name('organizations.players.destroy');
        Route::get('/organizations/{organization}/players/{player}/matches', [PlayerController::class, 'matches'])->name('organizations.players.matches');

        Route::get('/organizations/{organization}/player-invitations', [PlayerInvitationController::class, 'index'])->name('organizations.player-invitations.index');
        Route::post('/organizations/{organization}/player-invitations', [PlayerInvitationController::class, 'store'])->name('organizations.player-invitations.store');
        Route::delete('/organizations/{organization}/player-invitations/{playerInvitation}', [PlayerInvitationController::class, 'destroy'])->name('organizations.player-invitations.destroy');

        Route::get('/organizations/{organization}/friendly-matches', [FriendlyMatchController::class, 'index'])->name('organizations.friendly-matches.index');
        Route::post('/organizations/{organization}/friendly-matches', [FriendlyMatchController::class, 'store'])->name('organizations.friendly-matches.store');
        Route::get('/organizations/{organization}/friendly-matches/{friendlyMatch}', [FriendlyMatchController::class, 'show'])->name('organizations.friendly-matches.show');
        Route::put('/organizations/{organization}/friendly-matches/{friendlyMatch}', [FriendlyMatchController::class, 'update'])->name('organizations.friendly-matches.update');
        Route::delete('/organizations/{organization}/friendly-matches/{friendlyMatch}', [FriendlyMatchController::class, 'destroy'])->name('organizations.friendly-matches.destroy');

        Route::get('/organizations/{organization}/teams', [TeamController::class, 'index'])->name('organizations.teams.index');
        Route::post('/organizations/{organization}/teams', [TeamController::class, 'store'])->name('organizations.teams.store');
        Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::post('/teams/{team}/players', [TeamController::class, 'storePlayer'])->name('teams.players.store');
        Route::delete('/teams/{team}/players/{player}', [TeamController::class, 'destroyPlayer'])->name('teams.players.destroy');

        Route::get('/teams/{team}/coaches', [TeamCoachController::class, 'index'])->name('teams.coaches.index');
        Route::post('/teams/{team}/coaches', [TeamCoachController::class, 'store'])->name('teams.coaches.store');
        Route::get('/teams/{team}/coaches/{coach}', [TeamCoachController::class, 'show'])->name('teams.coaches.show');
        Route::put('/teams/{team}/coaches/{coach}', [TeamCoachController::class, 'update'])->name('teams.coaches.update');
        Route::delete('/teams/{team}/coaches/{coach}', [TeamCoachController::class, 'destroy'])->name('teams.coaches.destroy');

        Route::get('/organizations/{organization}/leagues', [LeagueController::class, 'index'])->name('organizations.leagues.index');
        Route::post('/organizations/{organization}/leagues', [LeagueController::class, 'store'])->name('organizations.leagues.store');
        Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
        Route::put('/leagues/{league}', [LeagueController::class, 'update'])->name('leagues.update');
        Route::delete('/leagues/{league}', [LeagueController::class, 'destroy'])->name('leagues.destroy');
        Route::get('/leagues/{league}/matches', [LeagueMatchController::class, 'index'])->name('leagues.matches.index');
        Route::get('/leagues/{league}/matches/{match}', [LeagueMatchController::class, 'show'])->name('leagues.matches.show');
        Route::put('/leagues/{league}/matches/{match}', [LeagueMatchController::class, 'update'])->name('leagues.matches.update');

        Route::get('/organizations/{organization}/competitions', [CompetitionController::class, 'index'])->name('organizations.competitions.index');
        Route::post('/organizations/{organization}/competitions', [CompetitionController::class, 'store'])->name('organizations.competitions.store');
        Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])->name('competitions.show');
        Route::put('/competitions/{competition}', [CompetitionController::class, 'update'])->name('competitions.update');
        Route::delete('/competitions/{competition}', [CompetitionController::class, 'destroy'])->name('competitions.destroy');
        Route::post('/competitions/{competition}/start', [CompetitionController::class, 'start'])->name('competitions.start');
        Route::post('/competitions/{competition}/complete', [CompetitionController::class, 'complete'])->name('competitions.complete');
        Route::post('/competitions/{competition}/reset', [CompetitionController::class, 'reset'])->name('competitions.reset');

        Route::get('/competitions/{competition}/join-requests', [CompetitionJoinRequestController::class, 'index'])->name('competitions.join-requests.index');
        Route::post('/competitions/{competition}/join-requests', [CompetitionJoinRequestController::class, 'store'])->name('competitions.join-requests.store');
        Route::put('/competitions/{competition}/join-requests/{joinRequest}', [CompetitionJoinRequestController::class, 'update'])->name('competitions.join-requests.update');

        Route::get('/competitions/{competition}/groups', [TournamentGroupController::class, 'index'])->name('competitions.groups.index');
        Route::get('/competitions/{competition}/groups/{group}', [TournamentGroupController::class, 'show'])->name('competitions.groups.show');

        Route::get('/competitions/{competition}/matches', [CompetitionMatchController::class, 'index'])->name('competitions.matches.index');
        Route::get('/competitions/{competition}/matches/{match}', [CompetitionMatchController::class, 'show'])->name('competitions.matches.show');
        Route::put('/competitions/{competition}/matches/{match}', [CompetitionMatchController::class, 'update'])->name('competitions.matches.update');

        Route::get('/competitions/{competition}/team-matches', [TeamMatchController::class, 'index'])->name('competitions.team-matches.index');
        Route::get('/competitions/{competition}/team-matches/{teamMatch}', [TeamMatchController::class, 'show'])->name('competitions.team-matches.show');
        Route::put('/competitions/{competition}/team-matches/{teamMatch}', [TeamMatchController::class, 'update'])->name('competitions.team-matches.update');

        Route::get('/competitions/{competition}/standings', [StandingController::class, 'index'])->name('competitions.standings.index');
    });
});
