<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelCopoun extends Model
{
    use HasFactory;

    protected $fillable = [
        'reel_id',
        'copoun_name',
        'discount',
        'location',
        'expiry_date',
        'target_copouns',
        'copoun_price',
        'total_price'
    ];

    public function reel(){
        return $this->belongsTo(Reel::class, 'reel_id');
    }

    public function copounList(){
        return $this->hasMany(CopounList::class);
    }
}
