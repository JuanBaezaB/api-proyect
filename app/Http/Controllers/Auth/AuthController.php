<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $user = User::where("email", $request->email);
            if ($user) {
                if (auth()->attempt($data)) {
                    $token = auth()->user()->createToken('API Token')->accessToken;
                    return response()->json([
                        "success"=>true,
                        'user' => auth()->user(),
                        "token"=>$token,
                    ]);
                } else {
                    return response(["success"=>false,'message' => 'Usuario y/o constraseña invalido. Por favor, intente nuevamente.'], 401);
                }
            }
            return response()->json(["success" => false, "message" => "El usuario no se ha encontrado"]);

        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        try {
            return response()->json(["success"=>true,"user"=>$request->user()]);
        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }

    }

}
