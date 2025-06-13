<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Verifica el estado de autenticación del usuario y devuelve sus datos y el token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIsAdmin()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated or token invalid.'], 401);
            }

            $token = JWTAuth::fromUser($user);

            if (!$token) {

                // Si después de todo no hay token
                if(!$token) {
                    return response()->json(['message' => 'Token not found in request.'], 400);
                }
            }

            if($user->rol != 'Admin') {
                return response()->json(['message' => 'User not authenticated or token invalid.'], 401);
            }

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,

            ];

            return response()->json([
                'user' => $userData,
                'token' => $token,
            ]);

        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not process token: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
