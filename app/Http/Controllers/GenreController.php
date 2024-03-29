<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\EntityNotFoundException;
use App\Mappers\GenreMapper;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GenreController extends Controller
{
    private Genre $genre;
    private GenreMapper $mapper;

    public function __construct(Genre $genre, GenreMapper $mapper)
    {
        $this->genre = $genre;
        $this->mapper = $mapper;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = $this->genre->all();
        $dtos = $this->mapper->mapModelsToDTOs($genres);
        return response($dtos);
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
        $dto = $this->mapper->mapModelToDTO($genre);
        return response($dto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $genre = $this->getGenreById($id);
        $dto = $this->mapper->mapModelToDTO($genre);
        return response($dto);
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
        $oldImage = $genre->image;
        $genre->fill($request->all());
        if($hasImage) {
            $image = $request->file('image');
            $imageUrl = $image->store('imgs/genres', 'public');
            $genre->image = $imageUrl;
        }
        $genre->update();
        if($hasImage) {
            Storage::disk('public')->delete($oldImage);
        }
        $dto = $this->mapper->mapModelToDTO($genre);
        return response($dto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $genre = $this->getGenreById($id);
        $genre->movies;
        if($genre->movies->count() > 0) {
            throw new BadRequestException('This genre cannot be deleted, there is a movie register with it');
        }
        $genre->delete();
        return response(null, 204);
    }
}
