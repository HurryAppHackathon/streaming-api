<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\UserParty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PartyController extends Controller
{
    /**
     * Get a specific party
     * 
     * Gets a specific party by id
     */
    public function show(UserParty $userParty)
    {
        return response()->json(['data' => $userParty]);
    }

    /**
     * Create new party
     * 
     * Creates a new party
     */
    public function store(StorePartyRequest $request)
    {
        $validated = $request->validated();

        $directoryPath = '/parties/images';

        $imagePath = Storage::putFile($directoryPath, $request->file('image'));

        if (!$imagePath) {
            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('videos.upload_failed'));
        }

        $userParty = UserParty::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'image_url' => $imagePath,
            'invite_code' => mt_rand(10000000, 99999999),
        ]);

        return new PartyResource(UserParty::find($userParty->id));
    }
}
