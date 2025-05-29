<?php

namespace App\Http\Controllers\private\CustomerControllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreCustomerController extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {

        request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:customers|email',
            'phone' => 'required|min:1',
            'address' => 'required|string|max:255',
        ]);

        $datos = [
            'id' => Str::uuid()->toString(),
            'name' => $request->input("name"),
            'email' => $request->input("email"),
            'phone' => $request->input("phone"),
            'address' => $request->input("address"),
        ];

        $customer = new Customer($datos);

        $customer->save();

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address
        ]);
    }

}
