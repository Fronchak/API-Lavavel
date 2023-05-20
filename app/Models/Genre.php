<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'image'];

    public function rules() {
        return [
            'name' => 'required|max:60|unique:genres,name,' . $this->id,
            'image' => 'required|file|mimes:png,jpeg,jpg'
        ];
    }

    public function messages() {
        return [
            'required' => 'The :attribute is required',
            'image.photo' => 'Image should be a file image'
        ];
    }
}
