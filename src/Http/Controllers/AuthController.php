<?php

namespace Http\Controllers;

use Models\User;
use Framework\Core\Http\Request;
use Framework\Facades\Auth;

class AuthController extends Controller
{

    public function getAll(){
        return response()->json(User::all());
    }

    public function verifySignIn(Request $req)
    {
        try {
            Auth::attempt($req->only('login', 'password'));
        } catch (\Exception $e) { 
            $error = $e->getMessage();
            exit();
        }
    }




    public function verifyRegister(Request $req){
        if($req->password == $req->password2){
            if(empty(User::where('login', $req->login)->get())){
                $user = User::create([
                    'nom' => $req->fullname,
                    'login' => $req->login,
                    'password' => Hash::make($req->password)
                ]);
                Auth::log($user);
                exit();
            }
            else{
                $error = "Le nom d'utilisateur saisis est déjà utilisé";
            }
        }else{
            $error = "Les mots de passes saisis ne correspondent pas";
        }

    }
}
