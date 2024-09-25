<?php
namespace App\Services\PaymentGateway;

use App\DTOs\InitPaymentDTO;
use App\DTOs\PaymentDataDTO;

class PaymentService{

    public static $config;
    public function __construct()
    {
    }

    public static function initiateTransaction($payGateway, InitPaymentDTO $data){
        $service = self::register()[$payGateway]["initTransaction"];
        $resp = call_user_func([ new $service[0], $service[1]], $data);
        return $resp;
    }

    public static function verifyTransaction($payGateway, $data = []) : PaymentDataDTO
    {
        $service = self::register()[$payGateway]["verifyTransaction"];
        $resp = call_user_func([new $service[0], $service[1]], $data);
        return $resp;
    }

    protected static function register(){
        return [
            "paystack" => [
                "initTransaction" => [PaystackService::class, "initiateTransaction"],
                "verifyTransaction" => [PaystackService::class, "verifyTransaction"]
            ]
        ];
    }
}
