<?php

namespace App\Services\Apis\VTU;

use App\DTOs\VTU\BillingTransactionDTO;
use App\DTOs\VTU\VTUTransactionDTO;
use App\Enums\TransactionEnums;
use App\Http\Resources\Apis\MonnifyApiResource;
use App\Models\Billings\BillingTransaction;
use App\Models\User;
use App\Services\Apis\BaseApiService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class GsubzApiService extends BaseApiService
{
    public $accRef = "RF-";
    public function getDataPlans($service){
        $resp = $this->apiRequest()
        ->getRequest("plans", [
            "service" => $service
        ]);
        return $this->response($resp);
    }

    private function billResponse($resp){
        $data = $resp;
        return new BillingTransactionDTO(
            vendor_amount: $data["amountPaid"] ?? 0,
            vendor: "gsubz",
            reference: $data["transactionID"] ?? null,
            recipient: $data["phone"] ?? null,
            respCode: $resp["code"] ?? null,
            status: $data["status"] ?? null == "successful" ? TransactionEnums::successStatus : TransactionEnums::failedStatus,
            data: $resp
        );
    }

    public function buyData(VTUTransactionDTO $data) : BillingTransactionDTO
    {
       $resp =  $this->apiRequest()
       ->withHeaders([
        "Authorization" => "Bearer $this->apiKey",
       ])
        ->asForm()
        ->postRequest("/pay/", [
            "serviceID" => $data->serviceID,
            "plan" => $data->planID,
            "api" => $this->apiKey,
            "amount" => "",
            "phone" => $data->recipient,
            "requestID" => $data->reference,
        ]);
        $resp = $this->response($resp);
        return $this->billResponse($resp);
    }
    public function buyAirtime(VTUTransactionDTO $data): BillingTransactionDTO
    {
        $svid = str_replace("9mobile", "etisalat", $data->serviceID);
        $resp =  $this->apiRequest()
            ->withHeaders([
                "Authorization" => "Bearer $this->apiKey"
            ])
            ->asForm()
            ->postRequest("/pay/", [
                "serviceID" => $svid,
                "api" => $this->apiKey,
                "amount" => $data->amount,
                "phone" => $data->recipient,
                "requestID" => $data->reference,
            ]);
        $resp = $this->response($resp);
        return $this->billResponse($resp);
    }
}
