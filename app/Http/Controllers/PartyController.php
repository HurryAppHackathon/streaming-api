<?php

namespace App\Http\Controllers;

use App\Models\UserParty;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    /**
     * Get a specific party
     * 
     * Gets a specific party by id
     */
    function show(UserParty $userParty)
    {
        return response()->json(['data' => $userParty]);
    }
}
