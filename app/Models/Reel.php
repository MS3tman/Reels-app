<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;
    protected $table = 'reels';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function campains()
    {
        return $this->hasMany(Campain::class);
    }

    public function likes()
    {
        return $this->hasMany(ReelLike::class);
    }

    public function likedByUser($userId)
    {
        return (bool)$this->likes->where('user_id', $userId)->count();
    }

    public function loves()
    {
        return $this->hasMany(ReelLove::class);
    }

    public function loveByUser($userId)
    {
        return (bool)$this->loves->where('user_id', $userId)->count();
    }

    public function favourites()
    {
        return $this->hasMany(Fav::class);
    }

    public function FavByUser($userId)
    {
        return (bool)$this->favourites->where('user_id', $userId)->count();
    }
}
