<?php

namespace App\Http\Middleware;

use App\Models\PasswordReset;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\DocBlock\Tags\Method;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next)
    {
        $email = PasswordReset::where('email', $request->email)->orderBy('created_at', 'desc')->first();

        if (! Hash::check($request->token, $email->token) || ! $email || $email->is_used == true) {
            throw ValidationException::withMessages([
                'token' => 'your token is wrong'
            ]);
        }

        DB::table('password_resets')->where('email', $request->email)->update(['is_used' => true]);

        return $next($request);
    }
}
