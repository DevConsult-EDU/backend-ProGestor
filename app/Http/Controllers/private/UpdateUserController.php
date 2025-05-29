<?php

namespace App\Http\Controllers\private;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateUserController extends Controller
{

    public function __invoke(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($request->route('id'), 'id'),
            ]
        ];

        if (!empty($request->input('password'))) {
            $rules['password'] = 'required|string|min:6';
        }

        $validated = $request->validate($rules);

        $user = User::find($request->route("id"));

        $datos = [
            'name' => $request->input("name"),
            'email' => $request->input("email"),
            'rol' => $request->input("rol"),
        ];

        if (!empty($request->input('password'))) {
            $datos['password'] = Hash::make($request->input('password'));
        }

        $user->update($datos);

        return $user;
    }

}
