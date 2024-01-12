<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserAvatarRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function storeAvatar(StoreUserAvatarRequest $request)
    {
        $request->validated();

        $directoryPath = '/users/avatars';

        $avatarPath = Storage::putFile($directoryPath, $request->file('file'));

        if (!$avatarPath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('users.avatar_upload_failed'));
        }

        $user = $request->user();

        $user->avatar_url = $avatarPath;

        $user->save();

        return response()->json(['data' => User::find($user->id)]);
    }
}
