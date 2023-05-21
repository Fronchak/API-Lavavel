<?php

namespace App\Mappers;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;

class MovieMapper {

    private GenreMapper $genreMapper;

    public function __construct(GenreMapper $genreMapper)
    {
        $this->genreMapper = $genreMapper;
    }

    public function mapModelToDTO(Movie $movie) {
        $genres = $this->genreMapper->mapModelsToDTOs($movie->genres);
        return [
            'id' => $movie->id,
            'title' => $movie->title,
            'synopsis' => $movie->synopsis,
            'image' => $movie->image,
            'lauch_year' => $movie->lauch_year,
            'genres' => $genres
        ];
    }

    public function mapModelsToDTOs(Collection $movies) {
        return $movies->map(function(Movie $movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'image' => $movie->image,
                'lauch_year' => $movie->lauch_year
            ];
        });
    }
}

?>
