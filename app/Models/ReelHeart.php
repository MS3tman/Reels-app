<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelHeart extends Model
{
    use HasFactory;
    protected $table = 'reels_hearts';
    protected $fillable = ['reel_id', 'user_id'];
}
