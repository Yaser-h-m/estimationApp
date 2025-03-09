<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeagueController;


// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';

Route::get('/',[ LeagueController::class, 'index'])->name('league.index');
Route::get('/league', [LeagueController::class, 'index'])->name('league.index');
Route::get('/initialize', [LeagueController::class, 'initialize'])->name('league.initialize');
Route::get('/scheduled-matches', [LeagueController::class, 'scheduledMatches'])->name('league.scheduled-matches');
Route::get('/start-simulation', [LeagueController::class, 'startSimulation'])->name('league.start-simulation');
Route::get('/play-next-week', [LeagueController::class, 'playNextWeek'])->name('league.play-next-week');
Route::get('/play-all-matches', [LeagueController::class, 'playAllGamees'])->name('league.play-all-games');
Route::put('/games/{game}', [LeagueController::class, 'updateGame'])->name('league.update-game');
Route::post('/estimations', [LeagueController::class, 'submitEstimation'])->name('league.submit-estimation');

