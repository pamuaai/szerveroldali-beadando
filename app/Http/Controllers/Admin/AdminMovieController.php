<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class AdminMovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('movies.new-movie');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Validation
        $newMovie = Movie::create($this->validateMovieRequest());
        $this->storeImage($newMovie);
        return redirect()->route('movie', $newMovie);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Movie $movie)
    {
        //
        return view('movies.edit-movie', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Movie $movie)
    {
        //
        $movie->update($this->validateMovieRequest());
        $this->storeImage($movie);
        return redirect()->route('movie', $movie);
    }

    public function clearAllRatings(Movie $movie)
    {
        $movie->ratings()->delete();

        return redirect()->route('movie', $movie);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        //
        $movie->delete();
        return redirect()->route('home');
    }

    private function validateMovieRequest()
    {
        return request()->validate([
            'title' => 'required | max:255',
            'director' => 'required | max:128',
            'year' => 'required | numeric | min:1870 | max:' . date('Y'),
            'description' => 'max:512 | sometimes | nullable',
            'length' => 'required | min:0 | max: 51420 ',
            'image' => 'max:2048 | mimes:jpeg,jpg,png',
        ], [
            'title.required' => 'Kérjük Add meg a film címét!',
            'rating.regex' => 'Az értékelés egy 1 és 5 közötti szám legyen!',
        ]);
    }

    private function storeImage($movie)
    {
        if (request()->has('image'))
            $movie->update([
                'image' => request()->image->store('movies', 'public'),
            ]);
    }
}
