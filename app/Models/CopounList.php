<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopounList extends Model
{
    use HasFactory;

    protected $fillable = [
        'copoun_id',
        'copoun_code',
        'user_id',
    ];

    public function reelCopoun(){
        return $this->belongsTo(ReelCopoun::class, 'copoun_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
