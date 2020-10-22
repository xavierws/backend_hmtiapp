<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Issuing API token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        $userable = User::find($user->id)->userable;
        $role = $userable->role->name;

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($role === 'admin'){
            $token = $user->createToken($request->device_name, ['user:admin'])->plainTextToken;
        } else {
            $token = $user->createToken($request->device_name)->plainTextToken;
        }

        return response()->json([
            'token' => $token,
            'role' => $role
        ]);
    }

    /**
     * Change the user's password
     * Authentication required
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function changePassword(Request $request)
    {
        $request->validate([
//            'email' => 'required|email',
            'password' => 'required',
            'new_password' => 'required'
        ]);

        $user = $request->user();
//        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Password are incorrect.']
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => ['Your password has been changed successfully']
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $key = mt_rand(100000, 999999);

        $request->validate([
           'email' => 'required|email'
        ]);


    }

    public function resetPassword()
    {

    }
}
