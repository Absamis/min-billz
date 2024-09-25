<?php

namespace App\Http\Controllers\Billings\VTU;

use App\Classes\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billings\VTU\BuyAirtimeRequest;
use App\Http\Requests\Billings\VTU\BuyDataRequest;
use App\Http\Resources\Billings\VTU\DataBundleServiceResource;
use App\Interfaces\Billings\VTU\IAirtimeDataRepository;
use Illuminate\Http\Request;

class AirtimeDataController extends Controller
{
    //

    public IAirtimeDataRepository $airtimeDataRepo;
    public function __construct(IAirtimeDataRepository $airDt)
    {
        $this->airtimeDataRepo = $airDt;
    }
    public function getDataPlans(){
        $resp = $this->airtimeDataRepo->getDataPlans();
        return ApiResponse::success("Data plans fetched",  DataBundleServiceResource::collection($resp));
    }

    public function buyData(BuyDataRequest $request){
        $data = $request->validated();
        $resp = $this->airtimeDataRepo->buyData($data["plan_id"], $data["phone_number"]);
        return ApiResponse::success("Data bundle purchase successful", $resp);
    }

    public function buyAirtime(BuyAirtimeRequest $request)
    {
        $data = $request->validated();
        $resp = $this->airtimeDataRepo->buyAirtime($data["provider_id"], $data["amount"], $data["phone_number"]);
        return ApiResponse::success("Airtime purchase successful", $resp);
    }
}
