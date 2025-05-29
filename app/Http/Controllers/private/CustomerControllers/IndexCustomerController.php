<?php

namespace App\Http\Controllers\private\CustomerControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexCustomerController extends Controller
{

    public function __invoke(Request $request)
    {

        $customers = DB::table('customers')->get();

        $clientes = [];
        foreach ($customers as $customer) {
            $clientes[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'created_at' => $customer->created_at,
            ];
        }

        return response()->json($clientes);
    }

}
