<?php

namespace App\Models;

use App\Casts\PathToUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UserParty extends Model
{
    protected $guarded = [];
    protected $with = ['owner', 'users'];
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

    /**
     * The users that are in the party.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (UserParty $userParty) {
            $keys = ['name' => $userParty->name, 'video_url' => null, 'user_id' => $userParty->user_id, 'invite_code' => $userParty->invite_code];

            Redis::hMSet('parties:' . $userParty->id, $keys);
        });

        static::updated(function (UserParty $userParty) {
            if ($userParty->wasChanged('finished_at') && !is_null($userParty->finished_at)) {
                Redis::delete('parties:' . $userParty->id);
            }
        });
    }
}
