<?php

namespace App\Services\Apis\VTU;

use App\DTOs\InitPaymentDTO;
use App\DTOs\PaymentDataDTO;
use App\DTOs\VTU\VTUTransactionDTO;

class VTUService
{

    public static $config;
    public function __construct()
    {
    }

    public static function buyData($vendor, VTUTransactionDTO $data)
    {
        $service = self::register()[$vendor]["buyData"];
        $resp = call_user_func([new $service[0], $service[1]], $data);
        return $resp;
    }

    protected static function register()
    {
        return [
            "gsubz" => [
                "buyData" => [GsubzApiService::class, "buyData"]
            ]
        ];
    }
}
