<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Kiểm tra người dùng có quyền admin không
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Mối quan hệ với đơn hàng
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mối quan hệ với giỏ hàng
     */
    public function cart()
    {
        return $this->hasOne(Cart::class)->latest();
    }

    /**
     * Mối quan hệ với phim yêu thích
     */
    public function favoriteMovies()
    {
        return $this->belongsToMany(Movie::class, 'favorite_movies')
                    ->withTimestamps();
    }

    /**
     * Kiểm tra người dùng đã yêu thích phim chưa
     */
    public function hasFavorited(Movie $movie)
    {
        return $this->favoriteMovies()->where('movie_id', $movie->id)->exists();
    }

    /**
     * Thêm phim vào danh sách yêu thích
     */
    public function addFavorite(Movie $movie)
    {
        if (!$this->hasFavorited($movie)) {
            return $this->favoriteMovies()->attach($movie->id);
        }
        return false;
    }

    /**
     * Xóa phim khỏi danh sách yêu thích
     */
    public function removeFavorite(Movie $movie)
    {
        return $this->favoriteMovies()->detach($movie->id);
    }
}
