<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movies = Auth::check() && Auth::user()->is_admin ?
            Movie::withTrashed()->orderBy('year', 'DESC')->paginate(10) :
            Movie::orderBy('year', 'DESC')->paginate(10);

        return view('index', compact('movies'));
    }


    public function movie(Movie $movie)
    {
        $ratings = $movie->ratings()->orderBy('updated_at', 'DESC')->paginate(10);
        return view('movies.movie', compact('movie', 'ratings'));
    }

    public function topList()
    {
        $topMovies = Movie::withAvg(relation: 'ratings', column: 'rating')
            ->orderBy('ratings_avg_rating', 'DESC')
            ->take(6)
            ->get();
        return view('toplist', compact('topMovies'));
    }
}
