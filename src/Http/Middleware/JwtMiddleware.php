<?php

namespace Http\Middleware;

use Framework\Core\Http\Middleware;
use Framework\Core\Http\Request;
use Framework\Facades\Auth;

class JwtMiddleware extends Middleware{



    public function handle(Request $req, $next = null){

        try{
            if(Auth::check()){
                return true;    
            };
        }catch(\Exception $e){
            throw $e;
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }


}