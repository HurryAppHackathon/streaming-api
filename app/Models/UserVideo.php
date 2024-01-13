<?php

namespace App\Models;

use App\Casts\PathToUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserVideo extends Model
{
    protected $guarded = [];
    protected $with = ['owner'];
    protected $casts = [
        'is_public' => 'boolean',
        'url' => PathToUrl::class,
        'thumbnail_url' => PathToUrl::class,
    ];


    /**
     * Get the user that owns the video.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
