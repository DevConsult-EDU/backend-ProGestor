<?php

namespace App\Http\Controllers\private\CustomerControllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class DeleteCustomerController extends Controller
{

    public function __invoke()
    {
        $customer = Customer::find(request()->route("id"));

        $customer->delete();
    }

}
