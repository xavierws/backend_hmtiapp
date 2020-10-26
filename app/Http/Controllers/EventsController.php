<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Resources\Event as EventResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class EventsController extends Controller
{

    public function index()
    {
        return EventResource::collection(Event::all());
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

        Event::create([
            'calendar_id' => $request->calendar_id,
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'backgroundColor' => $request->background_color,
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

        return response()->json([
            'message' => 'post events is successful'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
