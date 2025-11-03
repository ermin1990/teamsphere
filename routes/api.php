<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {

    // Public leagues
    Route::get('/leagues', [App\Http\Controllers\Api\V1\LeagueController::class, 'index']);
    Route::get('/leagues/{league}', [App\Http\Controllers\Api\V1\LeagueController::class, 'show']);

    // Public matches
    Route::get('/matches', [App\Http\Controllers\Api\V1\MatchController::class, 'index']);
    Route::get('/matches/{match}', [App\Http\Controllers\Api\V1\MatchController::class, 'show']);

    // Public organizations
    Route::get('/organizations', [App\Http\Controllers\Api\V1\OrganizationController::class, 'index']);
    Route::get('/organizations/{organization}', [App\Http\Controllers\Api\V1\OrganizationController::class, 'show']);

    // Public players
    Route::get('/players', [App\Http\Controllers\Api\V1\PlayerController::class, 'index']);
    Route::get('/players/{player}', [App\Http\Controllers\Api\V1\PlayerController::class, 'show']);

    // Sports
    Route::get('/sports', [App\Http\Controllers\Api\V1\SportController::class, 'index']);
    Route::get('/sports/{sport}', [App\Http\Controllers\Api\V1\SportController::class, 'show']);
});

// Protected API routes (authentication required)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // User profile
    Route::get('/profile', [App\Http\Controllers\Api\V1\ProfileController::class, 'show']);
    Route::put('/profile', [App\Http\Controllers\Api\V1\ProfileController::class, 'update']);
    Route::put('/profile/password', [App\Http\Controllers\Api\V1\ProfileController::class, 'updatePassword']);

    // User's organizations
    Route::get('/my-organizations', [App\Http\Controllers\Api\V1\OrganizationController::class, 'myOrganizations']);
    Route::post('/organizations', [App\Http\Controllers\Api\V1\OrganizationController::class, 'store']);
    Route::put('/organizations/{organization}', [App\Http\Controllers\Api\V1\OrganizationController::class, 'update']);
    Route::delete('/organizations/{organization}', [App\Http\Controllers\Api\V1\OrganizationController::class, 'destroy']);

    // Organization leagues
    Route::get('/organizations/{organization}/leagues', [App\Http\Controllers\Api\V1\LeagueController::class, 'organizationLeagues']);
    Route::post('/organizations/{organization}/leagues', [App\Http\Controllers\Api\V1\LeagueController::class, 'store']);
    Route::put('/leagues/{league}', [App\Http\Controllers\Api\V1\LeagueController::class, 'update']);
    Route::delete('/leagues/{league}', [App\Http\Controllers\Api\V1\LeagueController::class, 'destroy']);

    // League matches
    Route::get('/leagues/{league}/matches', [App\Http\Controllers\Api\V1\MatchController::class, 'leagueMatches']);
    Route::post('/leagues/{league}/matches', [App\Http\Controllers\Api\V1\MatchController::class, 'store']);
    Route::put('/matches/{match}', [App\Http\Controllers\Api\V1\MatchController::class, 'update']);
    Route::delete('/matches/{match}', [App\Http\Controllers\Api\V1\MatchController::class, 'destroy']);

    // League players
    Route::get('/leagues/{league}/players', [App\Http\Controllers\Api\V1\PlayerController::class, 'leaguePlayers']);
    Route::post('/leagues/{league}/players', [App\Http\Controllers\Api\V1\PlayerController::class, 'store']);
    Route::put('/players/{player}', [App\Http\Controllers\Api\V1\PlayerController::class, 'update']);
    Route::delete('/players/{player}', [App\Http\Controllers\Api\V1\PlayerController::class, 'destroy']);

    // Match results/scoring
    Route::post('/matches/{match}/score', [App\Http\Controllers\Api\V1\MatchController::class, 'updateScore']);
    Route::post('/matches/{match}/status', [App\Http\Controllers\Api\V1\MatchController::class, 'updateStatus']);

    // Tables/standings
    Route::get('/leagues/{league}/standings', [App\Http\Controllers\Api\V1\TableController::class, 'standings']);
    Route::get('/leagues/{league}/tables', [App\Http\Controllers\Api\V1\TableController::class, 'index']);
    Route::post('/leagues/{league}/tables', [App\Http\Controllers\Api\V1\TableController::class, 'store']);
    Route::put('/tables/{table}', [App\Http\Controllers\Api\V1\TableController::class, 'update']);
    Route::delete('/tables/{table}', [App\Http\Controllers\Api\V1\TableController::class, 'destroy']);
});