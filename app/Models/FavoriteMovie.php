<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteMovie extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'movie_id'];

    /**
     * Mối quan hệ với người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với phim
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
