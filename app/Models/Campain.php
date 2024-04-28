<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campain extends Model
{
    use HasFactory;

    function reel() {
        return $this->belongsTo(Reel::class)->with('user');
    }

    function views() {
        return $this->hasMany(CampainViews::class);
    }
}
