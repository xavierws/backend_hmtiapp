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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

    public function updatepassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'new_password' => 'required|min:6'
        ]);


        $user = $request->user();
        //        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Password are incorrect.']
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

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
        $uniqueName = str_replace('/', '', Hash::make(mt_rand(100000, 999999)));
        $imageName = 'public/colleger/' . (string)$collegerId . $uniqueName . '.png';

        $oldImg = CollegerProfile::find($collegerId)->image;

        if ($oldImg->exists()) {
            $oldName = $oldImg->value('filename');
            Storage::delete($oldName);

            $oldImg->filename = $imageName;
            $oldImg->save();
        }

        Image::create([
            'filename' => $imageName,
            'imageable_id' => $collegerId,
            'imageable_type' => 'App\Models\CollegerProfile'
        ]);

        Storage::put($imageName, $image);

//        Image::updateOrCreate([
//            'filename' => $imageName,
//            'imageable_id' => $collegerId,
//            'imageable_type' => 'App\Models\CollegerProfile'
//        ]);

//        if (!Image::where([
//            ['imageable_type', 'App\Models\CollegerProfile'],
//            ['imageable_id', $collegerId]
//        ])->exists()) {
//            Image::create([
//                'filename' => $imageName,
//                'imageable_id' => $collegerId,
//                'imageable_type' => 'App\Models\CollegerProfile'
//            ]);
//        }

        return response()->json([
            'message' => 'data updated'
        ]);
    }
}
