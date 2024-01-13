<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyUserVideoRequest;
use App\Http\Requests\ShowVideoRequest;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateUserVideoRequest;
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
     * Get a specific video
     * 
     * Gets a specific video by id
     */
    public function show(ShowVideoRequest $request, UserVideo $userVideo)
    {
        $request->validated();

        return response()->json(['data' => $userVideo]);
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
        $thumbnailsPath = '/thumbnails/' . $user->id;

        $videoPath = Storage::putFile($directoryPath, $request->file('file'));

        if (!$videoPath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.upload_failed'));
        }

        $thumbnailPath = Storage::putFile($thumbnailsPath, $request->file('thumbnail'));

        if (!$thumbnailPath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.upload_failed'));
        }

        $video = UserVideo::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'url' => $videoPath,
            'thumbnail_url' => $thumbnailPath,
            'is_public' => $validated['is_public'],
        ]);

        return response(['data' => UserVideo::with(['owner'])->find($video->id)]);
    }

    /**
     * Update a user video
     * 
     * Updates a user video
     */
    public function update(UpdateUserVideoRequest $request, UserVideo $userVideo)
    {
        $validated = $request->validated();

        if (isset($validated['name']) && !is_null($validated['name'])) {
            $userVideo->name = $validated['name'];
        }
        if (isset($validated['description']) && !is_null($validated['description'])) {
            $userVideo->description = $validated['description'];
        }
        if (isset($validated['is_public']) && !is_null($validated['is_public'])) {
            $userVideo->is_public = $validated['is_public'];
        }
        if (isset($validated['thumbnail']) && !is_null($validated['thumbnail'])) {
            $thumbnailsPath = '/thumbnails/' . $request->user()->id;
            $thumbnailPath = Storage::putFile($thumbnailsPath, $request->file('thumbnail'));

            if (!$thumbnailPath) {
                return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.upload_failed'));
            }

            $userVideo->thumbnail_url = $thumbnailPath;
        }

        $userVideo->save();

        return response(['data' => UserVideo::find($userVideo->id)]);
    }

    /**
     * Delete a user video
     * 
     * Deletes a user video given an id
     */
    public function destroy(DestroyUserVideoRequest $request, UserVideo $userVideo)
    {
        $request->validated();


        $filePath = $this->getFilePathFromUrl($userVideo->url);

        if (!$filePath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.delete_failed'));
        }

        $deleted = Storage::delete($filePath);

        if (!$deleted) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.delete_failed'));
        }

        $userVideo->delete();

        return response()->json(['data' => (object) []]);
    }

    private function getFilePathFromUrl(string $url)
    {
        $url = parse_url($url);

        try {
            $path = implode("/", array_slice(explode('/', $url['path']), 2));
        } catch (mixed $e) {
            return null;
        }

        return $path;
    }
}
