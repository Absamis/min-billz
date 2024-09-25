<?php
namespace App\Services\PaymentGateway;

use App\DTOs\InitPaymentDTO;
use App\DTOs\PaymentDataDTO;
use App\Enums\TransactionEnums;
use Illuminate\Support\Facades\Crypt;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaystackService{

    public function initiateTransaction(InitPaymentDTO $payData){
        $data = [
            "amount" => $payData->amount * 100,
            "currency" => $payData->currency,
            "email" => $payData->customerEmail,
            "reference" => $payData->reference,
            "metadata" => $payData->metadata,
            "callback_url" => route("web.trans.verify", ["transaction" => $payData->reference])
        ];
        $dt = Paystack::getAuthorizationUrl($data);
        return $dt;
    }

    public function verifyTransaction($data){
        $ref = $data["reference"] ?? null;
        $resp =(object)Paystack::getPaymentData($ref);
        if(!$resp->status)
            abort(400, "Invalid reference id");
        $resp = (object)$resp->data;
        $response = new PaymentDataDTO(
            amount: $resp->amount/100,
            pay_reference: $resp->reference,
            id: $resp->reference,
            metadata: $resp->metadata,
            transDate: date("Y-m-d H:i:s", strtotime($resp->transaction_date)),
            email: $resp->customer["email"],
            status: $resp->status == "success" ? TransactionEnums::successStatus : TransactionEnums::failedStatus,
            data: Crypt::encrypt($resp),
            charges: $resp->fees/100
        );
        return $response;
    }
}
