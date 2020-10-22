<?php

namespace App\Http\Controllers;

use App\Models\AdministratorProfile;
use App\Models\CollegerProfile;
use App\Models\User;
use App\Http\Resources\CollegerProfile as CollegerProfileResource;
use App\Http\Resources\AdministratorProfile as AdministratorProfileResource;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $user = User::find($user_id);
        if ($user->userable_type == 'App\Models\CollegerProfile') {
            return new CollegerProfileResource(CollegerProfile::find($user->userable_id));
        } else {
            return new AdministratorProfileResource(AdministratorProfile::find($user->userable_id));
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required',
            'birthday' => 'required',
            'address' => 'required|max:255'
        ]);

        $colleger = CollegerProfile::find($request->id);
        $colleger->name = $request->name;
        $colleger->birthday = $request->birthday;
        $colleger->address = $request->address;
        $colleger->save();

        return response()->json([
            'message' => 'data updated'
        ]);
    }
}
