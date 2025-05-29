<?php

namespace App\Http\Controllers\private\CustomerControllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class ShowCustomerController extends Controller
{

    public function __invoke(Request $request)
    {

        $customer = Customer::find($request->route("id"));

        return [
            "name" => $customer['name'],
            "email" => $customer['email'],
            "phone" => $customer['phone'],
            "address" => $customer['address'],
            "created_at" => $customer['created_at'],
            "updated_at" => $customer['updated_at'],
        ];
    }

}
