<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
                        "success" => true,
                        'user' => auth()->user(),
                        "token" => $token,
                    ]);
                } else {
                    return response(["success" => false, 'message' => 'Usuario y/o constraseña invalido. Por favor, intente nuevamente.'], 401);
                }
            }
            return response()->json(["success" => false, "message" => "El usuario no se ha encontrado"]);

        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                'password' => ['required', 'string']
            ]);
            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 422);
            } else {
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                ]);
                $token = $user->createToken('API Token')->accessToken;
                return response()->json([
                    "success" => true,
                    "message" => "Usuario creado exitosamente!",
                    'user' => $user,
                    "token" => $token,
                ], 201);
            }

        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }

    }


    public function logout(Request $request)
    {
        try {
            $accessToken = auth()->user()->token();
            $token= $request->user()->tokens->find($accessToken);
            $token->revoke();
            return response(["success"=>true ,'message' => 'Has cerrado sesión exitosamente.'], 200);

        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }

    }

    public function user(Request $request)
    {
        try {
            return response()->json(["success" => true, "user" => $request->user()]);
        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }

    }
    public function logoutUserAllDevices(Request $request) {
        try {
            $refreshTokenRepository = app(\Laravel\Passport\RefreshTokenRepository::class);
            $user = $request->user();
            foreach(User::find($user->id)->tokens as $token) {
                $token->revoke();
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }
            return response()->json(["success"=>true, "message"=>"Se ha cerrado sesión en todos los dispositivos."]);
        } catch (Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage()], 500);
        }
    }

    // public function validateToken(Request $request, $localCall = false)
    // {

    //     $psr = (new DiactorosFactory)->createRequest($request);

    //     try {
    //         $psr = $this->server->validateAuthenticatedRequest($psr);

    //         $token = $this->tokens->find(
    //             $psr->getAttribute('oauth_access_token_id')
    //         );

    //         $currentDate = new DateTime();
    //         $tokenExpireDate = new DateTime($token->expires_at);

    //         $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;

    //         if ($localCall) {
    //             return $isAuthenticated;
    //         } else {
    //             return json_encode(array('authenticated' => $isAuthenticated));
    //         }
    //     } catch (OAuthServerException $e) {
    //         if ($localCall) {
    //             return false;
    //         } else {
    //             return json_encode(array('error' => 'Something went wrong with authenticating. Please logout and login again.'));
    //         }
    //     }
    // }

}
