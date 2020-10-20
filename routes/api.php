<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
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


//reset password



//Route group for middleware
//Require login
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    //post the feed
    Route::post('feed/post', [FeedController::class, 'create']);

    //show all the feed
    Route::get('feed/index', [FeedController::class, 'index']);

    //delete specific feed
    Route::post('feed/delete', [FeedController::class, 'destroy']);

    //send a specific feed
    Route::get('feed/edit', [FeedController::class, 'edit']);

    //Update the feed data and image
    Route::put('feed/update', [FeedController::class, 'update']);

    //logout and revoke token
    Route::post('logout', function (Request $request) {
//        $request->validate([
//            'email' => 'required|email',
//        ]);
//        $user = \App\Models\User::where('email', $request->email)->first();
        $user = \App\Models\User::where('email', $request->user()->email)->first();

        $user->tokens()->delete();
        return response()->json([
            'message' => 'you are already logout'
        ]);
    });
});
