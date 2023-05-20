<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityNotFoundException;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GenreController extends Controller
{
    private Genre $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = $this->genre->all();
        return response($genres);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->genre->rules(), $this->genre->messages());
        $image = $request->file('image');
        $imageUrn = $image->store('imgs/genres', 'public');
        $genre = new Genre();
        $genre->fill($request->all());
        $genre->image = $imageUrn;
        $genre->save();
        return response($genre, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $genre = $this->getGenreById($id);
        return response($genre);
    }

    private function getGenreById($id): Genre {
        $genre = $this->genre->find($id);
        if($genre === null) {
            throw new EntityNotFoundException('Genre not found');
        }
        return $genre;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $genre = $this->getGenreById($id);
        $hasImage = true;
        $parameters = $request->all();
        $rules = $genre->rules();
        if(!array_key_exists('image', $parameters)) {
            unset($rules['image']);
            $hasImage = false;
        }
        $request->validate($rules, $genre->messages());
        $oldPhoto = $genre->image;
        $genre->fill($request->all());
        if($hasImage) {
            $photo = $request->file('image');
            $photoUrl = $photo->store('imgs/genres', 'public');
            $genre->image = $photoUrl;
        }
        $genre->update();
        if($hasImage) {
            Storage::disk('public')->delete($oldPhoto);
        }
        return response($genre);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        //
    }
}
