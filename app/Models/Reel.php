<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;
    protected $table = 'reels';

    public function views()
    {
        return $this->hasMany(ReelView::class);
    }

    public function clicks()
    {
        return $this->hasMany(ReelClick::class);
    }

    public function hearts()
    {
        return $this->hasMany(ReelHeart::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }
}
