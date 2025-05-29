<?php

namespace App\Http\Controllers\private;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexUserController extends Controller
{

    public function __invoke(Request $request)
    {

        $users = DB::table('users')->get();

        $usuarios = [];
        foreach ($users as $user) {
            $usuarios[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'created_at' => $user->created_at,
            ];
        }

        return response()->json($usuarios);
    }

}
