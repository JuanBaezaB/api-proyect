<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Usuario y/o constraseña invalido. Por favor, intente nuevamente.'], 401);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return response([
            'user' => auth()->user(),
            'token' => $token
        ]);
    }
}
