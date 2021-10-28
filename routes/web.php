<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use App\Models\Rating;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [MovieController::class, 'index']);

Route::get('/movie/{movie}', [MovieController::class, 'movie'])->name('movie');

Route::post('/movie/rate/{movie}', [RatingController::class, 'store'])->name('movie.rate');

Route::get('/toplist', [MovieController::class, 'toplist'])->name('toplist');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
