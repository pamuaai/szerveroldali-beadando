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
        return view('new-movie');
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

        $data = request()->validate([
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

        $newMovie = Movie::create($data);
        $this->storeImage($newMovie);
        return redirect()->route('movie', $newMovie);
    }

    private function storeImage($movie)
    {
        if (request()->has('image'))
            $movie->update([
                'image' => request()->image->store('movies', 'public'),
            ]);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
