<?php

namespace App\Http\Controllers\private;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ShowUserController extends Controller
{

    public function __invoke(Request $request)
    {

        $user = User::find($request->route("id"));

        return [
            "name" => $user['name'],
            "email" => $user['email'],
            "rol" => $user['rol'],
            "created_at" => $user['created_at'],
            "updated_at" => $user['updated_at'],
        ];
    }

}
