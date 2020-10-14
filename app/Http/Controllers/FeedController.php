<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Image;
use App\Http\Resources\Feed as FeedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'image_strings' => 'required',
            'title' => 'required|max:255',
            'caption' => 'required|max:3000',
            'day_of_week' => 'required'
        ]);
        $image_strings = $request->image_strings;

        Feed::create([
            'title' => $request->title,
            'caption' => $request->caption,
            'day_of_week' => $request->day_of_week
        ]);
        $feedId = Feed::orderBy('id', 'desc')->first()->value('id');

        $n = 0;
        foreach ($image_strings as $image_string) {
            $image = base64_decode($image_string);

            $n++;
            $imageName = 'public/feed/' . $request->title . (string)$n . '.png';
            Storage::put($imageName, $image);

            Image::create([
                'filename' => $imageName,
                'imageable_id' => $feedId,
                'imageable_type' => 'App\Models\Feed'
            ]);
        }

        return response()->json([
            'message' => 'post feed is successful'
        ]);

    }

    public function get()
    {
        return FeedResource::collection(Feed::orderBy('created_at', 'desc')->get());
    }

    public function delete()
    {

    }
}
