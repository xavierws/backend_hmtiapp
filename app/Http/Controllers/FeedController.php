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

        if (! $request->user()->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to post feed'
            ]);
        }

        //$image_strings = $request->image_strings;
        $image_string = $request->image_strings;
        Feed::create([
            'title' => $request->title,
            'caption' => $request->caption,
            'day_of_week' => $request->day_of_week
        ]);
        $feedId = Feed::orderBy('id', 'desc')->limit(1)->value('id');

        $n = 0;
      //  foreach ($image_strings as $image_string) {
            $image = base64_decode($image_string);

            $n++;
            $imageName = 'public/feed/' . $request->title . (string)$feedId . (string)$n . '.png';
            Storage::put($imageName, $image);

            Image::create([
                'filename' => $imageName,
                'imageable_id' => $feedId,
                'imageable_type' => 'App\Models\Feed'
            ]);
     //   }

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
            'feed_id' => 'required|integer'
        ]);

        if (! $request->user()->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to delete feed'
            ]);
        }

        $feed = Feed::find($request->feed_id);

        $n = 0;
        $filename = array();
        $id = array();
        foreach ($feed->images as $image) {
            $filename[$n] = $image->filename;
            $id[$n] = $image->id;
            $n++;
        }

        Storage::delete($filename);
        Image::destroy($id);
        $feed->delete();

        return response()->json([
           'message' => 'the feed has been deleted'
        ]);
    }

    /**
     * show the requested feed
     *
     * @param Request $request
     * @return FeedResource
     */
    public function edit(Request $request)
    {
        $request->validate([
            'feed_id' => 'required'
        ]);

        return new FeedResource(Feed::find($request->feed_id));
    }

    /**
     * Update the requested feed
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $request->validate([
            'feed_id' => 'required',
            'image_strings' => 'required',
            'title' => 'required|max:255',
            'caption' => 'required|max:3000'
        ]);

        if (! $request->user()->tokenCan('user:admin')) {
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
            $imageName = 'public/feed/' . $request->title . (string)$request->feed_id . (string)$n . '.png';
            Storage::put($imageName, $image);

            $feed->image->filename = $imageName;
            $feed->image->filename->save();
        }

        return response()->json([
            'message' => 'feed has been updated'
        ]);
    }
}
