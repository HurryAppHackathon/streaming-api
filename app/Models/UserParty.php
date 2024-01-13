<?php

namespace App\Models;

use App\Casts\PathToUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserParty extends Model
{
    protected $guarded = [];
    protected $with = ['owner'];
    protected $casts = [
        'image_url' => PathToUrl::class,
        'is_public' => 'boolean',
    ];
    protected $hidden = ['invite_code'];

    /**
     * Get the user that owns the video.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
