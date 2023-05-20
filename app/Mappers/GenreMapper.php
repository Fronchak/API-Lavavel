<?php

namespace App\Mappers;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

class GenreMapper {

    public function mapModelToDTO(Genre $genre) {
        return [
            'id' => $genre->id,
            'name' => $genre->name,
            'image' => $genre->image
        ];
    }

    public function mapModelsToDTOs(Collection $genres) {
        return $genres->map(function(Genre $genre) {
            return $this->mapModelToDTO($genre);
        });
    }

}

?>
