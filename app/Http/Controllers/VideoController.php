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
     * Get list of videos
     * 
     * Gets list of videos
     */
    public function index(Request $request)
    {
        $typeQuery = $request->query('type');

        $userVideos = UserVideo::query();

        switch ($typeQuery) {
            case 'public':
                $userVideos = $userVideos->where('is_public', true);
                break;
            case 'private':
                $userVideos = $userVideos->where('is_public', false)->where('user_id', $request->user()->id);
                break;
            case 'own':
                $userVideos = $userVideos->where('user_id', $request->user()->id);
                break;
            default:
                $userVideos = $userVideos->where('is_public', true)->orWhere('user_id', $request->user()->id);
                break;
        }

        $userVideos = $userVideos->get();

        return response()->json(['data' => $userVideos]);
    }

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

    /**
     * Delete a user video
     * 
     * Deletes a user video given an id
     */
    public function destroy(Request $request, UserVideo $userVideo)
    {
        if ($request->user()->id !== $userVideo->user_id) {
            return abort(Response::HTTP_NOT_FOUND, __('videos.not_found'));
        };

        $userVideo->delete();

        return response()->json(['data' => (object) []]);
    }
}
