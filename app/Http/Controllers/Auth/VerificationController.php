<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Enums\AccountEnums;
use App\Events\UserAccountVerified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Interfaces\Auth\IVerificationRepository;
use App\Interfaces\IUserRepository;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class VerificationController extends Controller
{
    //
    public IVerificationRepository $vrfRepo;
    public IUserRepository $userRepo;
    public function __construct(IVerificationRepository $vrf, IUserRepository $usr)
    {
        $this->vrfRepo = $vrf;
        $this->userRepo = $usr;
    }

    public function verifyAccount(VerifyAccountRequest $request)
    {
        $data = $request->all();
        $vrf = $this->vrfRepo->verifyCode($data["code"], $data["token"]);
        switch($vrf->verification_type){
            case AccountEnums::accountVerificationType:
                $this->userRepo->verifyAccount($vrf->user);
                UserAccountVerified::dispatch($vrf->user);
                return ApiResponse::success("Account verified successfully");
            case AccountEnums::passwordVerificationType:
                $psvrf = $this->vrfRepo->setVerificationData($vrf->user, AccountEnums::resetPasswordVerificationType);
                return ApiResponse::success("Account verified. Reset your password", new VerificationResource($psvrf));
            case AccountEnums::pinVerificationType:
                $psvrf = $this->vrfRepo->setVerificationData($vrf->user, AccountEnums::resetPinVerificationType);
                return ApiResponse::success("Account verified. Reset your pin", new VerificationResource($psvrf));
            default:
                return ApiResponse::success("Account verified.");
        }
    }
    public function resendVerificationCode(Request $request){
        $token = $request->input("token");
        $vrf = $this->vrfRepo->verifyResendToken($token);
        !$vrf ? abort(400, "Invalid request") : null;
        $vrD = $this->vrfRepo->getVerificationCode($vrf->user, $vrf->verification_type);
        Notification::send($vrf->user, new VerificationCodeNotification($vrD));
        return ApiResponse::success("Verification code resent", new VerificationResource($vrD));
    }
}
