<?php

namespace Http\Controllers;

use Models\User;
use Models\Role;
use Models\UserRole;
use Framework\Core\Request;

class AdminController extends Controller
{

    public function index(){
        
        $users = User::all();
        $roles = Role::all();
        // echo Renderer::render('auth/panel/index.php', compact('users', 'roles'));
    }


    public function updateUserRole(Request $req, $id){
        $user_roles = UserRole::where('user_id', $id)->get();
        foreach($user_roles as $user_role){
            $user_role->remove();
        }
        foreach($req->roles as $role){
            UserRole::create([
                'user_id' => $id,
                'role_id' => $role
            ]);
        }
        header('location:/admin/panel');
    }
    

}
