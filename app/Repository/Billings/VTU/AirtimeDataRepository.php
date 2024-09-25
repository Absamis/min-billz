<?php

namespace App\Repository\Billings\VTU;

use App\DTOs\VTU\BillingTransactionDTO;
use App\DTOs\VTU\VTUTransactionDTO;
use App\DTOs\WalletTransDTO;
use App\Enums\BillingEnums;
use App\Enums\TransactionEnums;
use App\Interfaces\Billings\VTU\IAirtimeDataRepository;
use App\Models\Billings\BillingTransaction;
use App\Models\Billings\VTU\DataBundlePlan;
use App\Models\Billings\VTU\DataBundleService;
use App\Models\ServiceProvider;
use App\Services\Apis\VTU\GsubzApiService;
use App\Services\Apis\VTU\VTUService;
use App\Services\Transactions\TransactionService;
use App\Services\Transactions\WalletService;
use App\Traits\BillingTransTrait;

class AirtimeDataRepository implements IAirtimeDataRepository
{
    use BillingTransTrait;
    /**
     * Create a new class instance.
     */
    public $gsubzService;
    public $walletService;
    public function __construct(GsubzApiService $gserv, WalletService $walletService)
    {
        //
        $this->walletService = $walletService;
        $this->gsubzService = $gserv;
    }

    public function getDataPlans(){
        $data = DataBundleService::active()->get();
        return $data;
    }

    public function buyAirtime($prov_id, $amount, $phone): BillingTransaction
    {
        $prov = ServiceProvider::find($prov_id);
        $this->validateProvider($prov, "airtime");
        $ref = TransactionService::getTransactionID();
        $nar = "$prov->name airtime purchase for $phone";
        $dto = new VTUTransactionDTO(
            amount: $amount,
            recipient: $phone,
            serviceID: $prov->code,
            reference: $ref
        );
        $walletDTO = new WalletTransDTO(
            amount: $amount,
            log_rec: BillingEnums::airtimePurchaseType,
            reference: $ref,
            narration: $nar
        );
        $this->walletService->blockFunds(auth()->user()->wallet, $walletDTO);
        $resp = $this->gsubzService->buyAirtime($dto);
        if ($resp->respCode != "200") {
            $this->walletService->refundFunds(auth()->user()->wallet, $walletDTO);
            abort(200, "Airtime purchase failed. Try again", ["data" => $resp]);
        }
        $this->walletService->chargeFunds(auth()->user()->wallet, $walletDTO);

        $resp =$this->updateBillTrans($resp, new updateBillTransDTO(
            id: $ref,
            amount: $amount,
            service_id: $prov_id,
            trans_type: BillingEnums::airtimePurchaseType,
            sv_name: $prov->name,
            sv_type: "airtime",
            payMeth: TransactionEnums::paymentMethods["Wallet"],
            narr: $nar
        ));
        return $resp;
    }

    public function buyData($plan_id, $phone) : BillingTransaction
    {
        $plan = DataBundlePlan::findOrFail($plan_id);
        $planService = $plan->service ?? abort(400, "Invalid request. Try again");
        $ref = TransactionService::getTransactionID();
        $nar = $plan->service_name . " data purchase for $phone";
        $dto = new VTUTransactionDTO(
            amount: null,
            recipient: $phone,
            serviceID: $planService->code,
            planID: $plan->service_plan_id,
            reference: $ref,
            merchant_id: null
        );
        $walletDTO = new WalletTransDTO(
            amount: $plan->price,
            log_rec: BillingEnums::dataPurchaseType,
            reference: $ref,
            narration: $nar
        );
        $this->walletService->blockFunds(auth()->user()->wallet, $walletDTO);
        $resp = $this->gsubzService->buyData($dto);
        if($resp->respCode != "200"){
            $this->walletService->refundFunds(auth()->user()->wallet, $walletDTO);
            abort(200, "Data purchase failed. Try again", ["data" => $resp]);
        }
        $this->walletService->chargeFunds(auth()->user()->wallet, $walletDTO);
        $resp = $this->updateBillTrans($resp, new updateBillTransDTO(
            id: $ref,
            amount: $plan->price,
            service_id: $plan_id,
            trans_type: BillingEnums::dataPurchaseType,
            sv_name: $plan->service->name,
            sv_type: $plan->name,
            payMeth: TransactionEnums::paymentMethods["Wallet"],
            narr: $nar
        ));
        return $resp;
    }

    private function updateBillTrans(BillingTransactionDTO $resp, updateBillTransDTO $uptrans){
        $resp->id = $uptrans->id;
        $resp->amount = $uptrans->amount;
        $resp->service_id = $uptrans->service_id;
        $resp->transaction_type = $uptrans->trans_type;
        $resp->service_name = $uptrans->sv_name;
        $resp->service_type = $uptrans->sv_type;
        $resp->narration = $uptrans->narr;
        $resp->payment_method = $uptrans->payMeth;
        $resp = $this->logTransaction($resp);
        return $resp;
    }
}


class updateBillTransDTO{
    public $id;
    public $amount;
    public $service_id;
    public $trans_type;
    public $sv_name;
    public $sv_type;
    public $payMeth;
    public $narr = null;
    public function __construct
    (
        $id, $amount, $service_id, $trans_type, $sv_name, $sv_type, $payMeth, $narr = null
    ){
        $this->id = $id;
        $this->amount = $amount;
        $this->service_id = $service_id;
        $this->trans_type = $trans_type;
        $this->sv_name = $sv_name;
        $this->sv_type = $sv_type;
        $this->payMeth = $payMeth;
        $this->narr = $narr;
    }
}
