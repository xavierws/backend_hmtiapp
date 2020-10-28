<?php

namespace App\Http\Controllers;

use App\Actions\PushNotification;
use App\Models\Calendar;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Resources\Event as EventResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class EventsController extends Controller
{

    /**
     * list all the event
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'offset' => 'required|integer'
        ]);

        $calendars = Calendar::all()->offset($request->offset)->limit($request->limit);
        //        $events = Event::all();
        //        $events->groupBy('calendar_id')->toArray();

        $arr = array();
        foreach ($calendars as $calendar) {
            if ($calendar->events()->exists()) {
                $events = $calendar->events;

                $arr[] = [
                    'calendar_id' => $calendar->id,
                    'day' => $calendar->date,
                    'contain' => $events
                ];
            }
        }

        return response($arr);

        //        return EventResource::collection(Event::all());
    }

//    public function notif($title, $description)
//    {
//        $data = [
//            "to" => "/topics/event",
//            "notification" =>
//            [
//                "title" => $title,
//                "body" => $description
//            ],
//        ];
//        $dataString = json_encode($data);
//
//        $headers = [
//            'Authorization: key=AAAA7DnAwoc:APA91bEiqEGplmavyMQZzT4iqmU-RDpmGyE6CLYr31aBWsiLGRZymQlZhyqbeNyPfJyt-Uqxi0TXgrm-TPCkDYMFMvcMArpw-2s5pwet0IrbP_4ayyJdk5JYjJg24SFXsIf6BQro0r66',
//            'Content-Type: application/json',
//        ];
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
//
//        curl_exec($ch);
//    }


    /**
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getmarkeddates()
    {
        $calendars = Calendar::all();
        //        $events = Event::all();
        //        $events->groupBy('calendar_id')->toArray();

        $arr = array();
        foreach ($calendars as $calendar) {
            if ($calendar->events()->exists()) {
                $events = $calendar->events;

                $arr[$calendar->date] = [
                    'dotColor' => $events[0]->background_color,
                    'marked' => true
                ];
            }
        }

        return response($arr);

        //        return EventResource::collection(Event::all());
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $request->validate([
            'calendar_date' => 'required',
            'name' => 'required|max:255',
            'category' => 'required|max:255',
            'description' => 'required|max:255',
            'background_color' => 'required|max:255',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        if (!$request->user()->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to post events'
            ]);
        }
        $calendar2 = Calendar::where('date', $request->calendar_date)->first();
        Event::create([
            'calendar_id' => $calendar2->id,
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'background_color' => $request->background_color,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        //        $event = new Event;
        //        $event->id_calendar = $request->id_calendar;
        //        $event->name = $request->name;
        //        $event->category = $request->category;
        //        $event->description =  $request->description;
        //        $event->backgroundColor = $request->backgroundColor;
        //        $event->startdate =  $request->startdate;
        //        $event->enddate = $request->enddate;
        //        $event->save();

        PushNotification::handle('new event', $request->name);

        return response()->json([
            'message' => 'post events is successful'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return EventResource
     */
    public function edit(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer'
        ]);

        return new EventResource(Event::find($request->event_id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer',
            'calendar_id' => 'required|integer',
            'name' => 'required|max:255',
            'category' => 'required|max:255',
            'description' => 'required|max:255',
            'background_color' => 'required|max:255',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        if (!$request->user()->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to post events'
            ]);
        }

        $event = Event::find($request->event_id);

        $event->calendar_id = $request->calendar_id;
        $event->name = $request->name;
        $event->category = $request->category;
        $event->description =  $request->description;
        $event->background_color = $request->background_color;
        $event->start_date =  $request->start_date;
        $event->end_date = $request->end_date;
        $event->save();

        return response()->json([
            'message' => 'event is updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer'
        ]);

        if (!$request->user()->tokenCan('user:admin')) {
            throw ValidationException::withMessages([
                'user_level' => 'you do not have permission to post events'
            ]);
        }

        Event::find($request->event_id)->delete();

        return response()->json([
            'message' => 'event is deleted'
        ]);
    }
}
