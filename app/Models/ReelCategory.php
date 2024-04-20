<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'reel-id',
        'category_title'
    ];

    public function reels(){
        return $this->belongsTo(Reel::class, 'reel_id');
    }
}
