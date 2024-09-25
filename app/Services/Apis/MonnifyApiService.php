<?php

namespace App\Services\Apis;

use App\Http\Resources\Apis\MonnifyApiResource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MonnifyApiService extends BaseApiService
{
    public $accRef = "RF-";
    private function getAccessToken(){
        $token = Cache::remember("monnify_token", 2000, function(){
            $req = $this->apiRequest()
                ->withBasicAuth()
                ->postRequest("/v1/auth/login");
            $resp = $this->response($req);
            if(count($resp) == 0)
                abort(200, "Something went wrong");
            if($resp["responseCode"] != "0")
                abort(200, "Something went wrong");
            return $resp["responseBody"]["accessToken"];
        });
        return $token;
    }
    public function createReserveAccounts(User $user, $data = [])
    {
        $token = $this->getAccessToken();
        $req = $this->apiRequest($token)->postRequest("/v2/bank-transfer/reserved-accounts", [
            "accountReference" =>config("app.code").$user->id,
            "accountName" => $user->name,
            "currencyCode" => "NGN",
            "contractCode" => config("services.pay_gateway.monnify.contract_code"),
            "customerEmail" => $user->email,
            "customerName" => $user->name,
            "getAllAvailableBanks" => true
        ]);
        $resp = $this->response($req);
        if(!$resp || $resp["responseCode"] != "0")
            abort(200, "Something went wrong", ["data" => $resp]);
        return $resp["responseBody"];
    }


    public function verifyWebhook($data, $hashedData){
        $stringifiedData = json_encode($data);
        $computedHash = self::computeSHA512TransactionHash($stringifiedData, $this->apiSecret);
        if($computedHash != $hashedData)
            abort(400, "Invalid request", ["data" => [$computedHash, $hashedData]]);
    }

    private static function computeSHA512TransactionHash($stringifiedData, $clientSecret)
    {
        $computedHash = hash_hmac('sha512', $stringifiedData, $clientSecret);
        return $computedHash;
    }
}
