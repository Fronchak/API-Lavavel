<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'synopsis', 'lauch_year', 'image'];

    public function genres() {
        return $this->belongsToMany(Genre::class, 'movie_genres');
    }

    public function rules() {
        return [
            'title' => 'required|max:255',
            'synopsis' => 'required',
            'lauch_year' => 'required|integer|min:1900|max:2023',
            'image' => 'required|file|mimes:jpeg,jpg,png',
            'genres' => 'required|array|min:1|exists:genres,id'
        ];
    }

    public function messages() {
        return [
            'required' => 'The :attribute is required',
            'image.mimes' => 'Image should be a file image',
            'genres.min' => 'The movie must have at least one genre'
        ];
    }
}
