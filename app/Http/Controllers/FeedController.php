<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Image;
use App\Http\Resources\Feed as FeedResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FeedController extends Controller
{
    /**
     * Create a feed
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $request->validate([
            'image_strings' => 'required',
            'title' => 'required|max:255',
            'caption' => 'required|max:3000',
            'day_of_week' => 'required'
        ]);

        $user = User::where('email', $request->user()->email)->first();
        if (! $user->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to post feed'
            ]);
        }

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

    /**
     * show all the feed
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return FeedResource::collection(Feed::orderBy('created_at', 'desc')->get());
    }

    /**
     * Delete the corresponding feed
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'feed_id' => 'required|number'
        ]);

        $user = User::where('email', $request->user()->email)->first();
        if (! $user->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to delete feed'
            ]);
        }

        $feed = Feed::find($request->feed_id);
        Storage::delete($feed->images->filename);
        Image::destroy($feed->images->id);
        $feed->delete();

        return response()->json([
           'message' => 'the feed has been deleted'
        ]);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'feed_id' => 'required'
        ]);

        return new FeedResource(Feed::find($request->id));
    }

    public function update(Request $request)
    {
        $request->validate([
            'feed_id' => 'required',
            'image_strings' => 'required',
            'title' => 'required|max:255',
            'caption' => 'required|max:3000'
        ]);

        $user = User::where('email', $request->user()->email)->first();
        if (! $user->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to delete feed'
            ]);
        }

        $feed = Feed::find($request->feed_id);
        $feed->title = $request->title;
        $feed->caption = $request->caption;
        $feed->save();

        $n = 0;
        foreach ($request->image_strings as $image_string) {
            $image = base64_decode($image_string);

            $n++;
            $imageName = 'public/feed/' . $request->title . (string)$n . '.png';
            Storage::put($imageName, $image);

            $feed->image->filename = $imageName;
            $feed->image->filename->save();
        }

        return response()->json([
            'message' => 'feed has been updated'
        ]);
    }
}
