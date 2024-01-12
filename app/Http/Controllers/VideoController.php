<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoRequest;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Upload a new video
     * 
     * Uploads a new video publicly or to user's library
     */
    public function store(StoreVideoRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $directoryPath = '/videos/' . $user->id;

        $videoPath = Storage::putFile($directoryPath, $request->file('file'));

        if (!$videoPath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.upload_failed'));
        }
        
        $videoUrl = Storage::url($videoPath);

        $video = UserVideo::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'url' => $videoUrl,
            'is_public' => $validated['is_public'],
        ]);

        return response(['data' => UserVideo::with(['owner'])->find($video->id)]);
    }
}
