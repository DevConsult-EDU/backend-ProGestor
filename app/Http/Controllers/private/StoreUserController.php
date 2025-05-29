<?php

namespace App\Http\Controllers\private;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreUserController extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {
        request()->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users|email',
                'password' => 'required|string|min:1',
                'rol' => 'required|string|in:admin,user',
            ]
        );

        $datos = [
            'id' => Str::uuid()->toString(),
            'name' => $request->input("name"),
            'email' => $request->input("email"),
            'password' => $request->input("password"),
            'rol' => $request->input("rol"),
        ];

        $user = new User($datos);

        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'rol' => $user->rol,
        ]);
    }

}
