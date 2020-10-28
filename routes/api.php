<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//login and issue token
Route::post('login', [AuthController::class, 'login']);

//Send key to the user's email
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

//check token
Route::post('check-token', [AuthController::class, 'checkToken']);

//reset password
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('check.token');


//Route group for middleware
//Require login
Route::middleware('auth:sanctum')->group(function () {

    //get the user's credentials
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $role = $user->userable->role_id == 1? 'colleger':'admin';
        return response()->json([
            'user' => $user,
            'role' => $role
        ]);
    });

    //post the feed
    Route::post('feed/post', [FeedController::class, 'create']);

    //show all the feed
    Route::get('feed/index', [FeedController::class, 'index']);
    Route::get('feed/searchfeed', [FeedController::class, 'searchfeed']);
    //delete specific feed
    Route::delete('feed/delete', [FeedController::class, 'destroy']);

    //send a specific feed
    Route::get('feed/edit', [FeedController::class, 'edit']);

    //Update the feed data and image
    Route::put('feed/update', [FeedController::class, 'update']);

    //record the viewer of feed
    Route::post('feed/viewer/input', [FeedController::class, 'inputViewer']);

    //get all feed's viewer
    Route::get('feed/viewer', [FeedController::class, 'viewer']);

    //return the participant's profile
    Route::get('user/profile', [ParticipantController::class, 'index']);

    //update colleger data
    Route::put('user/update', [ParticipantController::class, 'update']);

    //create an event
    Route::post('event/post', [EventsController::class, 'create']);

    //get all events
    Route::get('event/index', [EventsController::class, 'index']);

    //get all marked dates
    Route::get('event/markeddates', [EventsController::class, 'getmarkeddates']);

    //get an event
    Route::get('event/edit', [EventsController::class, 'edit']);

    //update an event
    Route::put('event/update', [EventsController::class, 'update']);

    //delete event
    Route::delete('event/delete', [EventsController::class, 'destroy']);

    //get all dates from calendar
    Route::get('calendar/index', [CalendarController::class, 'index']);

    //logout and revoke token
    Route::post('logout', function (Request $request) {
        $user = \App\Models\User::where('email', $request->user()->email)->first();

        $user->tokens()->delete();
        return response()->json([
            'message' => 'you are already logout'
        ]);
    });
});
