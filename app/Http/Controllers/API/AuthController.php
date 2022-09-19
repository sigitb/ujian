<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'username' => 'required|unique:users,username',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) {
            $data = [
                "status" => "fail",
                "message" => "Failed register",
                "data" => $validator->errors()
            ];
            return response()->json($data,400);
        }
       

        try {
            User::create([
                "username" => $request->username,
                "password" => bcrypt($request->password) 
            ]);
            $data = [
                "status" => "success",
                "message" => "Successfully register",
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
           return response()->json($th->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) {
            $data = [
                "status" => "fail",
                "message" => "Failed login",
                "data" => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $username    = $request->username;
        $password = $request->password;

        if (Auth::attempt(["username" => $username, "password" => $password])) {
            $user = User::where("username", $username)->first();
            $token = $user->createToken('masjid')->plainTextToken;
            $data = [
                "status" => "fail",
                "message" => "successfully login",
                "data" => $token
            ];
            return response()->json($data, 200);
        }else{
            $data = [
                "status" => "fail",
                "message" => "gagal login",
            ];
            return response()->json($data, 400);
        }
    }
}
