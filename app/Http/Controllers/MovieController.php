<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityNotFoundException;
use App\Mappers\MovieMapper;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    private Movie $movie;
    private MovieMapper $movieMapper;

    public function __construct(Movie $movie, MovieMapper $movieMapper) {
        $this->movie = $movie;
        $this->movieMapper = $movieMapper;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = $this->movie->all();
        $dtos = $this->movieMapper->mapModelsToDTOs($movies);
        return response($dtos, 200);
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
        $dto = $this->movieMapper->mapModelToDTO($movie);
        return response($dto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $movie = $this->getMovieById($id);
        $dto = $this->movieMapper->mapModelToDTO($movie);
        return response($dto, 200);
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
    public function update(Request $request, $id)
    {
        $movie = $this->getMovieById($id);
        $hasImage = true;
        $parameters = $request->all();
        $rules = $movie->rules();
        if(!array_key_exists('image', $parameters)) {
            unset($rules['image']);
            $hasImage = false;
        }
        $request->validate($rules, $movie->messages());
        $oldImage = $movie->image;
        $movie->fill($request->all());
        if($hasImage) {
            $image = $request->file('image');
            $imageUrn = $image->store('imgs/movies', 'public');
            $movie->image = $imageUrn;
        }
        $movie->update();
        $genres = $request->input('genres');
        $movie->genres()->sync($genres);
        if($hasImage) {
            Storage::disk('public')->delete($oldImage);
        }
        $movie = $this->movie->with(['genres'])->find($id);
        $dto = $this->movieMapper->mapModelToDTO($movie);
        return response($dto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = $this->getMovieById($id);
        $movie->delete();
        return response(null, 204);
    }
}
