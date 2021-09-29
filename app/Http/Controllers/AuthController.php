<?php

namespace Http\Controllers;

use Models\User;
use Framework\Core\Http\Request;
use Framework\Facades\Auth;
use Framework\Facades\Hash;
use Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{

    public function current()
    {
        return response()->json(auth()->user());
    }


    public function login(LoginRequest $req)
    {
        $credentials = $req->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $data =  [
            'user' => auth()->user(),
            'token' => $token
        ];
        return response()->json($data);
    }



    public function verifySignIn(Request $req)
    {
        try {
            Auth::attempt($req->only('email', 'password'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            exit();
        }
    }



    public function register(Request $req)
    {
        if (!User::where('email', $req->email)->exists()) {
            $user = User::create([
                'lastname' => $req->lastname,
                'firstname' => $req->firstname,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);
            $credentials = $req->only('email', 'password');
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $data =  [
                'user' => auth()->user(),
                'token' => $token
            ];
            return response()->json($data);
        } else {
            return response()->json(["error" => "L'adresse email saisie est déjà enregistré"], 401);
        }
    }
}
