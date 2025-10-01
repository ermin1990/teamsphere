<?php

use App\Http\Controllers\LeagueController;
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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Organization routes
    Route::resource('organizations', OrganizationController::class);

    // League routes (nested under organizations)
    Route::get('organizations/{organization}/leagues/create', [LeagueController::class, 'create'])->name('organizations.leagues.create');
    Route::get('organizations/{organization}/leagues/{league}', [LeagueController::class, 'show'])->name('organizations.leagues.show');
    Route::post('organizations/{organization}/leagues', [LeagueController::class, 'store'])->name('organizations.leagues.store');
    Route::put('organizations/{organization}/leagues/{league}', [LeagueController::class, 'update'])->name('organizations.leagues.update');
    Route::delete('organizations/{organization}/leagues/{league}', [LeagueController::class, 'destroy'])->name('organizations.leagues.destroy');
    Route::get('leagues/settings-form', [LeagueController::class, 'getSettingsForm'])->name('leagues.settings-form');

    // League teams and players routes
    Route::post('organizations/{organization}/leagues/{league}/teams', [LeagueController::class, 'addTeam'])->name('organizations.leagues.teams.store');
    Route::post('organizations/{organization}/leagues/{league}/players', [LeagueController::class, 'addPlayer'])->name('organizations.leagues.players.store');
    Route::post('organizations/{organization}/leagues/{league}/add-players', [LeagueController::class, 'addPlayers'])->name('organizations.leagues.addPlayers');
    Route::patch('organizations/{organization}/leagues/{league}/start', [LeagueController::class, 'startLeague'])->name('organizations.leagues.start');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}', [LeagueController::class, 'showMatch'])->name('organizations.leagues.matches.show');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}/edit', [LeagueController::class, 'editMatch'])->name('organizations.leagues.matches.edit');
    Route::put('organizations/{organization}/leagues/{league}/matches/{match}', [LeagueController::class, 'updateMatch'])->name('organizations.leagues.matches.update');
    Route::get('organizations/{organization}/leagues/{league}/matches/{match}/live', [LeagueController::class, 'liveScore'])->name('organizations.leagues.matches.live');
    Route::post('organizations/{organization}/leagues/{league}/matches/{match}/live-score', [LeagueController::class, 'updateLiveScore'])->name('organizations.leagues.matches.live-score');
    Route::post('organizations/{organization}/leagues/{league}/matches/{match}/reset', [LeagueController::class, 'resetMatch'])->name('organizations.leagues.matches.reset');

    // Player routes (nested under organizations)
    Route::resource('organizations.players', PlayerController::class)->shallow();
});

require __DIR__.'/auth.php';
