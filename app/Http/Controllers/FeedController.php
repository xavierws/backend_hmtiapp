<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Image;
use App\Http\Resources\Feed as FeedResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $feed = Feed::orderBy('id', 'desc')->limit(1);

        $n = 0;
      //  foreach ($image_strings as $image_string) {
            $image = base64_decode($image_string);

            $n++;
            $hashedName = str_replace('/', '', Hash::make($feed->value('updated_at') . $feed->value('title')));
            $imageName = 'public/feed/' . (string)$feed->value('id') . (string)$n . $hashedName . '.png';
            Storage::put($imageName, $image);

            Image::create([
                'filename' => $imageName,
                'imageable_id' => $feed->value('id'),
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
        //return response(Feed::orderBy('created_at', 'desc')->get());
    }

    public function searchfeed(Request $request)
    {
        return FeedResource::collection(Feed::where('title','LIKE','%'.$request->search.'%')->orWhere('caption','LIKE','%'.$request->search.'%')->orderBy('created_at', 'desc')->get());
        //return response(Feed::orderBy('created_at', 'desc')->get());
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

        if (! Feed::find($request->feed_id)->exists())
            throw ValidationException::withMessages([
                'message' => 'id is wrong'
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
//        foreach ($request->image_strings as $image_string) {
        foreach ($feed->images as $images){
            Storage::delete($images->filename);
            $image = base64_decode($request->image_strings);

            $n++;
            $hashedName = str_replace('/', '', Hash::make($feed->value('updated_at') . $feed->value('title')));
            $imageName = 'public/feed/' . (string)$request->feed_id . (string)$n . $hashedName . '.png';
            Storage::put($imageName, $image);


            $images->filename = $imageName;
            $images->save();
        }

//        }

        return response()->json([
            'message' => 'feed has been updated'
        ]);
    }

    /**
     * record the feed's viewer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inputViewer(Request $request)
    {
        $request->validate([
            'feed_id' => 'required|integer',
//            'colleger_id' => 'required|integer'
        ]);

        if ($request->user()->userable_type == 'App\Models\CollegerProfile') {
            if (! DB::table('seen_by')->where([
                ['colleger_profile_id', $request->user()->userable_id],
                ['feed_id', $request->feed_id]
            ])->exists()) {
                Feed::find($request->feed_id)->collegerProfiles()->attach($request->user()->userable_id);

                return response()->json([
                   'message' => 'view recorded'
                ]);
            } else {
                return response()->json([
                   'message' => 'view has been recorded before'
                ]);
            }
        } else {
            return response()->json([
               'message' => 'you are an admin, bitch'
            ]);
        }
    }

    /**
     * get all the feed's viewer
     *
     * @param Request $request
     * @return array
     */
    public function viewer(Request $request)
    {
        $request->validate([
           'feed_id' => 'required|integer'
        ]);
        $feed = Feed::find($request->feed_id);

        $n = 0;
        $arrayOfName = array();
        foreach ($feed->collegerProfiles as $collegerProfile) {
            $arrayOfName[$n] = $collegerProfile->name;
            $n++;
        }

        return response(Feed::find($request->feed_id)->collegerProfiles()->get());
    }
}
