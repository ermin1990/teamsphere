<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\PlayerMatchController;
use App\Http\Controllers\Api\V1\SportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Auth (throttled - these are unauthenticated brute-force/enumeration targets)
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/auth/google', [AuthController::class, 'google'])->name('auth.google');
    });

    // Public reference data
    Route::get('/sports', [SportController::class, 'index'])->name('sports.index');
    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::get('/me/competitions', [AuthController::class, 'myCompetitions'])->name('auth.competitions');
        Route::get('/me/competitions/{competition}', [CompetitionController::class, 'myCompetition'])->name('me.competitions.show');
        Route::get('/me/matches', [PlayerMatchController::class, 'index'])->name('me.matches.index');
        Route::get('/me/matches/upcoming', [PlayerMatchController::class, 'upcoming'])->name('me.matches.upcoming');
        Route::get('/me/matches/completed', [PlayerMatchController::class, 'completed'])->name('me.matches.completed');
        Route::post('/me/competitions/{competition}/matches', [PlayerMatchController::class, 'store'])->name('me.matches.store');
        Route::put('/me/matches/{match}/result', [PlayerMatchController::class, 'updateResult'])->name('me.matches.result.update');

        Route::get('/competitions', [CompetitionController::class, 'publicIndex'])->name('competitions.index');
    });
});
