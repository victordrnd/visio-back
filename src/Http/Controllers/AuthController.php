<?php

namespace Http\Controllers;

use Models\User;
use Framwork\Core\Http\Request;
use Framwork\Facades\Auth;

class AuthController extends Controller
{

    public function signIn()
    {
        // echo Renderer::render('/auth/signin.php');
    }


    public function verifySignIn(Request $req)
    {
        try {
            Auth::attempt($req->only('login', 'password'));
        } catch (\Exception $e) { 
            $error = $e->getMessage();
            // echo Renderer::render('/auth/signin.php', compact('error'));
            exit();
        }
        header('location:/admin/panel');
    }


    public function register(){
        // echo Renderer::render('/auth/register.php');
    }


    public function verifyRegister(Request $req){
        if($req->password == $req->password2){
            if(empty(User::where('login', $req->login)->get())){
                $user = User::create([
                    'nom' => $req->fullname,
                    'login' => $req->login,
                    'password' => password_hash($req->password, PASSWORD_DEFAULT)
                ]);
                Auth::log($user);
                header('location:/');
                exit();
            }
            else{
                $error = "Le nom d'utilisateur saisis est déjà utilisé";
            }
        }else{
            $error = "Les mots de passes saisis ne correspondent pas";
        }
        // echo Renderer::render('/auth/register.php', compact('error'));

    }


    public function logout(){
        session_destroy();
        header('location:/');
    }
}
