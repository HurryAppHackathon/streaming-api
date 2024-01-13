<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (PersonalAccessToken $personalAccessToken) {
            if ($personalAccessToken->tokenable_type === User::class) {
                Redis::set('tokens:' . $personalAccessToken->tokenable_id, $personalAccessToken->token, $personalAccessToken->expires_at);
            }
        });
    }
}
