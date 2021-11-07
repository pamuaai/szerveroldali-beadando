<?php

use App\Http\Controllers\Admin\AdminMovieController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Models\Movie;

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

Route::get('/', [MovieController::class, 'index'])->name('home');
Route::get('/trashedlist', function () {
    dd(Movie::onlyTrashed()->get());
})->name('trashed');

Route::get('/logout', [UserController::class, 'logout']);

Route::get('/movie/{movie}', [MovieController::class, 'movie'])->name('movie');

Route::post('/movie/rate/{movie}', [RatingController::class, 'store'])->name('movie.rate');

Route::get('/toplist', [MovieController::class, 'toplist'])->name('toplist');


Route::post('/new-movie/store', [MovieController::class, 'store'])->name('movie.store');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'isAdmin'])
    ->group(function () {
        Route::post('/movie/{movie}/ratings/clear', [AdminMovieController::class, 'clearAllRatings'])->name('movie.rating.clear');
        Route::resource('movies', AdminMovieController::class);
        Route::get('/movies/restore/{movie}', [AdminMovieController::class, 'restore'])->name('movies.restore');
        Route::get('/deleted-movie/{movie}', [AdminMovieController::class, 'showDeletedMovie'])->name('deleted.movie');

        // Route::get('/new-movie', [AdminMovieController::class, 'create'])->name('movies.create');
    });


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
