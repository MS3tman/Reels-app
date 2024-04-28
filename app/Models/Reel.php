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

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function campains()
    {
        return $this->hasMany(Campain::class);
    }
}
