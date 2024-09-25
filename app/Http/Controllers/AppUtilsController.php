<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponse;
use App\Models\ServiceProvider;
use App\Models\Transactions\PaymentMethod;
use Illuminate\Http\Request;

class AppUtilsController extends Controller
{
    //
    public function getPaymentMethods(){
        return ApiResponse::success("Payment method fetched", PaymentMethod::active()->get());
    }

    public function getServiceProviders(Request $request){
        $catg = $request->input("category");
        $providers = ServiceProvider::query();
        if($catg){
            $providers = $providers->where("category", "LIKE", "%$catg%");
        }
        $providers = $providers->get();
        return ApiResponse::success("Service provider fetched", $providers);
    }
}
