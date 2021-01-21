<?php

namespace App\Http\Middleware;


use Closure;

class Bearer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $token = $request->bearerToken();


        if(isset($token) == true){

            $user = \App\User::where('api_token', $token)->first();

            if(!$user){

                $otheruser = \App\SuperAdmin::where('api_token', $token)->first();

                if(!$otheruser){
                    return response()->json(['message' => 'Invalid Authorization Key'], 401);
                }

            }

        }
        else{
            return response()->json(['message' => 'Please provide Authorization Key'], 401);
        }


        

        return $next($request);
    }
}
