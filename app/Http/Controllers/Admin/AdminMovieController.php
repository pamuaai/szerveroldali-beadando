<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Support\Facades\Storage;

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
        request()->session()->flash('movie_stored', true);
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

    public function showDeletedMovie($movieId)
    {
        $movie = Movie::onlyTrashed()->where('id', $movieId)->firstOrFail();
        // $ratings = $movie->ratings->orderBy('updated_at', 'DESC')->paginate(10);
        return app('App\Http\Controllers\MovieController')->movie($movie);
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
        request()->session()->flash('movie_edited', true);
        return redirect()->route('movie', $movie);
    }

    public function clearAllRatings(Movie $movie)
    {
        $movie->ratings()->delete();
        request()->session()->flash('ratings_cleared', true);
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
        request()->session()->flash('movie_deleted', true);
        return redirect()->route('admin.deleted.movie', $movie);
    }

    public function restore($movieId)
    {
        //
        $movie = Movie::onlyTrashed()->where('id', $movieId)->firstOrFail();
        $movie->restore();
        request()->session()->flash('movie_restored', true);
        return redirect()->route('movie', $movie);
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
        if (request()->has('deleteImage') && $movie->image) {
            Storage::delete($movie->image);
            $movie->update([
                'image' => null,
            ]);
        }
        if (request()->has('image')) {
            if ($movie->image) {
                Storage::delete($movie->image);
            }
            $movie->update([
                'image' => request()->image->store('movies', 'public'),
            ]);
        }
    }
}
