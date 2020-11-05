<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\PasswordReset;
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

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $userable = User::find($user->id)->userable;
        $role = $userable->role->name;

        if ($role === 'admin') {
            $token = $user->createToken($request->device_name, ['user:admin'])->plainTextToken;
        } else {
            $token = $user->createToken($request->device_name)->plainTextToken;
        }

        return response()->json([
            'token' => $token,
            'role' => $role
        ]);
    }
    //    public function notif($name,$description)
    //    {
    //        $data = [
    //            "to" => "/topics/event",
    //            "notification" =>
    //            [
    //                "title" => $name,
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
    //
    //    }
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

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Password are incorrect.']
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Your password has been changed successfully'
        ]);
    }

    public function coba()
    {

        $startDate = date_create("2013-03-15");
        $currentTime = date_create("2013-03-16");
        $diff = date_diff($startDate, $currentTime);
        if ($diff->format("%R%a") == "+1") {
           echo "asdasdasd";
        }
    }

    /**
     * get a key token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'your email is wrong'
            ]);
        }

        $key = mt_rand(100000, 999999);
        PasswordReset::create([
            'email' => $request->email,
            'token' => Hash::make($key),
            'created_at' => now()
        ]);

        Mail::to($request->email)->send(new ForgotPassword($key));

        return response()->json([
            'email' => $request->email,
            'message' => 'your verification code has been sent to your email'
        ]);
    }

    /**
     * checking the key token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function checkToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);

        $email = PasswordReset::where('email', $request->email)->orderBy('created_at', 'desc')->first();

        if (!Hash::check($request->token, $email->token) || !$email || $email->is_used == true) {
            throw ValidationException::withMessages([
                'token' => 'your token is wrong'
            ]);
        }

        return response()->json([
            'token' => 'validated'
        ]);
    }

    /**
     * reset the old password with a new password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'your password has been reset, please login again'
        ]);
    }
}
