<?php

namespace App\Http\Controllers\Billings;

use App\Classes\ApiResponse;
use App\Enums\BillingEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billings\CreateGiveawayRequest;
use App\Http\Resources\Billings\GiveawayResource;
use App\Interfaces\Billings\IGiveawayRepository;
use App\Models\Billings\Giveaway;
use Illuminate\Http\Request;

class GiveawayController extends Controller
{
    //
    public $giveawayRepo;
    public function __construct(IGiveawayRepository $gvRepo)
    {
        $this->giveawayRepo = $gvRepo;
    }

    public function createGiveaway(CreateGiveawayRequest $request)
    {
        $data = $request->validated();
        $resp = $this->giveawayRepo->createGiveaway($data);
        return ApiResponse::success("Giveaway created successfull", $resp);
    }

    public function getGiveaways(Request $request, $code = null, Giveaway $giveaway = null){
        $filters = $request->all();
        $resp = $this->giveawayRepo->getGiveaways($code, $giveaway, $filters);
        if($code && !$giveaway)
            $resp = GiveawayResource::collection($resp);
        return ApiResponse::success("Giveaway fectched", $resp);
    }

    public function claimGiveaway(Request $request, Giveaway $giveaway){
        $resp = $this->giveawayRepo->claimGiveaway($giveaway, $request->all());
        return ApiResponse::success("Giveaway successfully claimed", $resp);
    }

    public function updateGiveawayStatus(Request $request, $code, Giveaway $giveaway = null){
        $sts = implode(",", [BillingEnums::activeGiveaway, BillingEnums::canceledGiveaway, BillingEnums::inactiveGiveaway ]);
        $data = $request->validate([
            "status" => ["required", "in:$sts"]
        ]);
        $resp = $this->giveawayRepo->updateGiveawayStatus($code, $giveaway, $data["status"]);
        return ApiResponse::success("Status updated", ($resp));
    }

    public function findGiveaway(Request $request, $code){
        $resp = $this->giveawayRepo->findGiveaway($code);
        return ApiResponse::success("Giveaway fetched", GiveawayResource::collection($resp));
    }
}
