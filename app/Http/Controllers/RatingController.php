<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store a newly created resource in storage. Or update an existing one
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Movie $movie)
    {
        if (!$movie->ratings_enabled || !Auth::check()) {
            return redirect()->route('movie', $movie);
        }
        $data = $request->validate([
            'rating' => 'required|regex:/[1-5]/',
            'comment' => 'required'
        ], [
            'rating.required' => 'Kérjük pontozd a filmet 1-től 5-ig!',
            'rating.regex' => 'Az értékelés egy 1 és 5 közötti szám legyen!',
        ]);

        $rating = Rating::where('movie_id', $movie->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($rating === null) {
            $rating = new Rating;
            $rating->movie_id = $movie->id;
            $rating->user_id = Auth::id();
        }
        $rating->comment = $data['comment'];
        $rating->rating = $data['rating'];
        $rating->save();
        $request->session()->flash('movie_rated', true);
        return redirect()->route('movie', $movie);
    }
}
