<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelLove extends Model
{
    use HasFactory;
    public $timestamps=  false;    
    protected $fillable = ['reel_id', 'user_id'];

    public function reel() {
        return $this->belongsTo(Reel::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
