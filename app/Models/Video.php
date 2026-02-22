<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video',
    ];

    protected $appends = ['video_url'];

    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return url('storage/' . $this->video);
        }
        return null;
    }


}
