<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityNotFoundException;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private Movie $movie;

    public function __construct(Movie $movie) {
        $this->movie = $movie;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = $this->movie->all();
        return response($movies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->movie->rules(), $this->movie->messages());
        $image = $request->file('image');
        $imageUrn = $image->store('imgs/movies', 'public');
        $movie = new Movie();
        $movie->fill($request->all());
        $movie->image = $imageUrn;
        $movie->save();
        $genres = $request->input('genres');
        $movie->genres()->attach($genres);
        $movie->genres;
        return response($movie, 201);
        //dd($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $movie = $this->getMovieById($id);
        return response($movie, 200);
    }

    private function getMovieById($id): Movie {
        $movie = $this->movie->with(['genres'])->find($id);
        if($movie === null) {
            throw new EntityNotFoundException('Movie not found');
        }
        return $movie;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
