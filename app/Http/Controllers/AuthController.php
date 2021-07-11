<?php

namespace Http\Controllers;

use Models\User;
use Framework\Core\Http\Request;
use Framework\Facades\Auth;
use Framework\Facades\JWT;
use Http\Requests\Auth\LoginRequest;
use Models\Country;

class AuthController extends Controller {

    public function getAll() {
        
        return Country::with('cities')->get();//User::orderBy('user_id')->with('roles')->get();
    }

    public function current(){
        return response()->json(auth()->user());
    }


    public function login(LoginRequest $req) {
        $credentials = $req->only('login', 'password');
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $data =  [
            'user' => auth()->user(),
            'token' => $token
        ];
        return response()->json($data);
    }



    public function verifySignIn(Request $req) {
        try {
            Auth::attempt($req->only('login', 'password'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            exit();
        }
    }




    public function verifyRegister(Request $req) {
        if ($req->password == $req->password2) {
            if (empty(User::where('login', $req->login)->get())) {
                $user = User::create([
                    'nom' => $req->fullname,
                    'login' => $req->login,
                    'password' => Hash::make($req->password)
                ]);
                Auth::log($user);
                exit();
            } else {
                $error = "Le nom d'utilisateur saisis est déjà utilisé";
            }
        } else {
            $error = "Les mots de passes saisis ne correspondent pas";
        }
    }
}
