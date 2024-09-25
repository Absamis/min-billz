<?php

namespace App\Repository\Billings;

use App\DTOs\WalletTransDTO;
use App\Enums\AppEnums;
use App\Enums\BillingEnums;
use App\Enums\TransactionEnums;
use App\Interfaces\Billings\IGiveawayRepository;
use App\Interfaces\Billings\VTU\IAirtimeDataRepository;
use App\Models\Billings\Giveaway;
use App\Models\Billings\VTU\DataBundlePlan;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Services\Transactions\TransactionService;
use App\Services\Transactions\WalletService;
use Exception;

class GiveawayRepository implements IGiveawayRepository
{
    /**
     * Create a new class instance.
     */
    public $walletService;
    public $airtimeDataRepo;
    public function __construct(WalletService $walletService, IAirtimeDataRepository $adRepo)
    {
        //
        $this->airtimeDataRepo = $adRepo;
        $this->walletService = $walletService;
    }

    public function getGiveaways($code = null, $giveaway = null, $filters = [])
    {
        $data = Giveaway::query()->where("userid", auth()->user()->id);
        $id = $giveaway->id ?? null;
        if($id){
            $data = $data->where("id", $id)
                    ->orWhere(function($query) use($id){
                        $query->whereRelation("earnings", "userid", "=", auth()->user()->id)->whereRelation("earnings", "giveaway_id", "=", $id);
                    })->first();
            return $data;
        }
        elseif($code){
            $data = $data->where("code", $code)->get();
            return $data;
        }else{
            $status = $filters["status"] ?? null;
            $type = $filters["type"] ?? null;
            if($status)
                $data = $data->where("status", $status);
            if($type)
                $data = $data->where("bill_type", $type);
            $data = $data->orderBy("created_at", "DESC")->paginate(pageCount());
            return [ "records" => $data->groupBy("code")->all(), "next" => $data->nextPageUrl()];
        }
    }

    public function findGiveaway($code){
        $data = Giveaway::active()->where("code", $code)->get();
        count($data) == 0 ? abort(200, "No active giveaway") : null;
        return $data;
    }

    public function claimGiveaway(Giveaway $gv, $data = []){
        $gv->status != BillingEnums::activeGiveaway ? abort(200, "Giveaway is no more available.") : null;
        $this->validateGiveaway($gv, $data);
        $claimer = $this->registerGivewawayClaimer($gv);
        $narr = "Giveaway fund from" . $gv->user->user_tag . ",ref: " . $gv->user->id . " to " . auth()->user()->user_tag . ",ref: " . auth()->user()->id;
        $this->walletService->transferBlockFunds($gv->user->wallet, auth()->user()->wallet, new WalletTransDTO(
            amount: $gv->unit_price,
            log_rec: BillingEnums::giveawayType,
            narration: $narr,
            reference: Giveaway::class . "|" . $gv->id
        ));
        switch($gv->bill_type){
            case BillingEnums::airtimeType:
                $resp = $this->airtimeDataRepo->buyAirtime($gv->service_id, $gv->unit_price, $data["phone_number"]);
                break;
            case BillingEnums::dataBundleType:
                $resp = $this->airtimeDataRepo->buyData($gv->service_id, $data["phone_number"]);
                break;
        }
        return $claimer;
    }

    private function registerGivewawayClaimer(Giveaway $gv){
        $gv->refresh();
        if ($gv->quantity_claimed >= $gv->quantity_bought)
            abort(200, "No more active giveaway");
        $this->validateGiveawayClaimer($gv);
        $gv->quantity_claimed += 1;
        $gv->save();
        $claimer = $gv->earnings()->create([
            "userid" => auth()->user()->id,
            "code" => $gv->code,
            "status" => TransactionEnums::successStatus
        ]);
        return $claimer;
    }

    private function validateGiveaway(Giveaway $gv, $data = []){
        if($gv->userid == auth()->user()->id)
            abort(400, "Invalid request. You can't claim your giveaway");
        $this->validateGiveawayClaimer($gv);
        if($gv->quantity_claimed >= $gv->quantity_bought)
            abort(200, "Giveaway is no more available. Come back later");
        if($gv->bill_type == BillingEnums::airtimeType || $gv->bill_type == BillingEnums::dataBundleType){
            $data["phone_number"] ?? abort(400, "Phone number is required");
        }
    }

    private function validateGiveawayClaimer(Giveaway $gv){
        $claimer = $gv->earnings()->with("giveaway")->where(["userid" => auth()->user()->id, "code" => $gv->code])->first();
        if ($claimer){
            abort(429, "You can't claim more than one giveaway", ["data" => $claimer]);
        }
    }

    public function updateGiveawayStatus($code, Giveaway $gv = null, $status)
    {
        if($gv){
            $res = $this->updateGiveawayItemStatus($gv, $status);
        }
        else{
            $res = Giveaway::where(["code" => $code, "userid" => auth()->user()->id])->get();
            foreach($res as $gv){
                $this->updateGiveawayItemStatus($gv, $status);
            }
        }
        return $res ;
    }

    public function updateGiveawayItemStatus(Giveaway $gv, $status){
        if ($gv->status == BillingEnums::expiredGiveaway || $gv->status == BillingEnums::canceledGiveaway) {
            return $gv;
        }
        if ($status == BillingEnums::canceledGiveaway){
            $qtyB = $gv->quantity_bought;
            $qtyC = $gv->quantity_claimed;
            $prc = $gv->unit_price;
            $refund = ($qtyB-$qtyC) * $prc;
            if($refund > 0){
                $this->walletService->refundFunds($gv->user->wallet, new WalletTransDTO(
                    amount: $refund,
                    log_rec: "giveaway",
                    narration: $gv->bill_type . " giveaway",
                    reference: $gv->code
                ));
            }
        }
        $gv->status = $status;
        $gv->save();
        return $gv;
    }

    public function createGiveaway($data)
    {
        $billTypes = $data["bill_types"];
        $services = $data["service_ids"];
        $unitPrices = $data["prices"];
        $qtys = $data["quantities"];
        $code = TransactionService::getGiveawayCode();
        $error = false;
        foreach($billTypes as $key => $bType){
            try{
                $svid = $services[$key];
                $qty = $qtys[$key];
                $prc = $unitPrices[$key];
                $totAmt = $svname = $svType = null;
                if($bType == BillingEnums::airtimeType){
                    $sv = ServiceProvider::findOrFail($svid);
                    $svname = $sv->name;
                    $totAmt = $qty*$prc;
                    $svType = "airtime";
                } elseif ($bType == BillingEnums::dataBundleType) {
                    $sv = DataBundlePlan::findOrFail($svid);
                    $svname = $sv->service->name;
                    $prc = $sv->price;
                    $totAmt = $qty * $prc;
                    $svType = $sv->name;
                }else{
                    abort(400, "Invalid request");
                }
                $this->walletService->blockFunds(auth()->user()->wallet, new WalletTransDTO(
                    amount: $totAmt,
                    log_rec: BillingEnums::giveawayType,
                    narration: $bType. " giveaway",
                    reference: $code
                ));
                Giveaway::create([
                    "userid" => auth()->user()->id,
                    "code" => $code,
                    "bill_type" => $bType,
                    "service_id" => $svid,
                    "service_name" => $svname,
                    "service_type" => $svType,
                    "unit_price" => $prc,
                    "quantity_bought" => $qty,
                    "total_amount" => $totAmt,
                    "status" => BillingEnums::activeGiveaway,
                    "expired_in" => appSettings()->giveaway_expires_in,
                ]);
            }catch(Exception $ex){
                $error = true;
            }
        }
        if($error){
            abort(200, "Not all giveaway are added.", ["data" => ["code" => $code]]);
        }
        return ["code" => $code];
    }
}
