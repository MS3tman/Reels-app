<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampainViews extends Model
{
    use HasFactory;
    public $timestamps=  false;
    protected $primaryKey = null;
 
    protected $fillable = ['campain_id', 'user_id'];

    public function campain() {
        return $this->belongsTo(Campain::class);
    }
}
