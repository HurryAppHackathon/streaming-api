<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserVideo extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
    ];


    /**
     * Get the user that owns the video.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
