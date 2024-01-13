<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndPartyRequest;
use App\Http\Requests\StorePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\UserParty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PartyController extends Controller
{
    /**
     * Get list of videos
     * 
     * Gets list of videos
     */
    public function index(Request $request)
    {
        $typeQuery = $request->query('type');

        $userParties = UserParty::query();

        switch ($typeQuery) {
            case 'public':
                $userParties = $userParties->where('is_public', true);
                break;
            case 'private':
                $userParties = $userParties->where('is_public', false)->where('user_id', $request->user()->id);
                break;
            case 'own':
                $userParties = $userParties->where('user_id', $request->user()->id);
                break;
            default:
                $userParties = $userParties->where('is_public', true)->orWhere('user_id', $request->user()->id);
                break;
        }

        $userParties = $userParties->get();

        return PartyResource::collection($userParties);
    }

    /**
     * Get a specific party
     * 
     * Gets a specific party by id
     */
    public function show(UserParty $userParty)
    {
        return new PartyResource($userParty);
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
            'invite_code' => random_int(10000000, 99999999),
            'is_public' => $validated['is_public'],
        ]);

        return new PartyResource(UserParty::find($userParty->id));
    }

    /**
     * End user's party
     * 
     * Ends a user party
     */
    public function end(EndPartyRequest $request, UserParty $userParty)
    {
        $request->validated();

        if (!is_null($userParty->finished_at)) {
            return abort(Response::HTTP_BAD_REQUEST, __('parties.already_finished'));
        }

        $userParty->finished_at = now();

        $userParty->save();

        return new PartyResource($userParty);
    }
}
