<?php

namespace App\Http\Controllers;

use App\Models\AdministratorProfile;
use App\Models\CollegerProfile;
use App\Models\Image;
use App\Models\User;
use App\Http\Resources\CollegerProfile as CollegerProfileResource;
use App\Http\Resources\AdministratorProfile as AdministratorProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
//            'id' => 'required|integer',
            'name' => 'required',
            'birthday' => 'required',
            'address' => 'required|max:255'
        ]);

        $colleger = CollegerProfile::find($request->user()->userable_id);
        $colleger->name = $request->name;
        $colleger->birthday = $request->birthday;
        $colleger->address = $request->address;
        $colleger->save();

        return response()->json([
            'message' => 'data updated'
        ]);
    }

    public function uploadPicture(Request $request)
    {
        $request->validate([
           'image_string' => 'required'
        ]);

        $collegerId = $request->user()->userable_id;
        $image_string = $request->image_string;

        $image = base64_decode($image_string);

        $imageName = 'public/colleger/' . (string)$collegerId . '.png';
        Storage::put($imageName, $image);

        if(! Image::where([
            ['imageable_type', 'App\Models\CollegerProfile'],
            ['imageable_id', $collegerId]
        ])->exists()){
            Image::create([
                'filename' => $imageName,
                'imageable_id' => $collegerId,
                'imageable_type' => 'App\Models\CollegerProfile'
            ]);
        }

    }
}
