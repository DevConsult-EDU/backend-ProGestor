<?php

namespace App\Http\Controllers\private\CustomerControllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateCustomerController extends Controller
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
            ],
            'phone' => 'required|min:1',
            'address' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $customer = Customer::find($request->route("id"));

        $datos = [
            'name' => $request->input("name"),
            'email' => $request->input("email"),
            'phone' => $request->input("phone"),
            'address' => $request->input("address"),
        ];

        $customer->update($datos);

        return $customer;
    }

}
