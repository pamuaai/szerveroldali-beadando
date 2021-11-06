<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function getRating()
    {
        return number_format($this->ratings->avg('rating'), 2);
    }
}
