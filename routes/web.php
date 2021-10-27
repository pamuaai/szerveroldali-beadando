<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

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
Route::post('/movie/rate', function (Request $request) {
    $request->validate([
        'rating' => 'required|regex:/[1-5]/',
        'comment' => 'required'
    ], [
        'rating.required' => 'Kérjük pontozd a filmet 1-től 5-ig!',
        'rating.regex' => 'Az értékelés egy 1 és 5 közötti szám legyen!',
    ]);
})->name('movie.rate');
Route::get('/toplist', [MovieController::class, 'toplist'])->name('toplist');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
