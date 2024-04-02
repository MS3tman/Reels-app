<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'hls_format_path',
        'manifest_file_path',
    ];
}
