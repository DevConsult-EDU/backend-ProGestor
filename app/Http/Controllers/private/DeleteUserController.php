<?php

namespace App\Http\Controllers\private;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = User::find($request->route("id"));

        $user->delete();
    }

}
