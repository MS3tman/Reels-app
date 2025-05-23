<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelComment extends Model
{
    use HasFactory;
    protected $table = 'reels_comments';

    public function reel() {
        return $this->belongsTo(Reel::class);
    }
}
