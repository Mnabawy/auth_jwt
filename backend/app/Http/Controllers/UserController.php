<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{

    public function register(Request  $request)
    {

        $user = User::where('email', $request['email'])->first();

        if ($user) {
            $response['status'] = 0;
            $response['code'] = '400';
            $response['message'] = 'this user already exist';
            return $response;
        }
        // create user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = 'user added successfully';
        return response()->json($response);
    }


    // login
    public function login()
    {
        $credentials = request(['email', 'password']);

        try {
            if (!JWTAuth::attempt($credentials)) {
                $response['status'] = 0;
                $response['code'] = 401;
                $response['data'] = null;
                $response['message'] = "email or password error";
                return response()->json($response);
            }
        } catch (JWTException $e) {
            $response['data'] = null;
            $response['code'] = 500;
            $response['message'] = "coud'n craete the token";
            return response()->json($response);
        }

        $user = auth()->user();
        $data['token'] = auth()->claims([
            'user_id' => $user->id,
            'email' => $user->email
        ])->attempt($credentials);

        $response['data'] = $data;
        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = "Login Successfully";
        return response()->json($response);
    }

    public function user($id)
    {
        $user = User::find($id);
        if ($user) {

            $response['message'] = 'user is exist';
            return response()->json($response);
        } else {
            $response['message'] = 'user not found';

            return response()->json($response);
        }
    }
}
