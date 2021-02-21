<?php

namespace Http\Middleware;

use Framework\Core\Http\Middleware;
use Framework\Core\Http\Request;
use Framework\Facades\Auth;

class JwtMiddleware extends Middleware{



    public function handle(Request $req, $next = null){

        try{
            $user = Auth::user();
            var_dump($user);
        }catch(\Exception $e){
            throw $e;
            return response()->json(['error' => $e->getMessage()], 401);
        }
        return true;

    }


}