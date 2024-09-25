<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponse;
use App\Services\Apis\MonnifyApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    //

    public $mfyService;
    public function __construct(MonnifyApiService $mfy)
    {
        $this->mfyService = $mfy;
    }
    public function monnifyPaymentWebhook(Request $request){
        $body = $request->all();
        // Storage::append("resp.txt", json_encode($body));
        $hashed = $request->header("monnify-signature");
        $this->mfyService->verifyWebhook($body, $hashed);
        return ApiResponse::success("Success", $body);
    }
}
