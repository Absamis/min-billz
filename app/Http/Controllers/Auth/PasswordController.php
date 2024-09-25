<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Enums\AccountEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Interfaces\Auth\IVerificationRepository;
use App\Interfaces\IUserRepository;
use App\Mail\PasswordChangedMail;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PasswordController extends Controller
{
    //
    public IVerificationRepository $vrfRepo;
    public IUserRepository $userRepo;
    public function __construct(IVerificationRepository $vrf, IUserRepository $userRepo)
    {
        $this->vrfRepo = $vrf;
        $this->userRepo = $userRepo;
    }


    public function forgotPassword(Request $request){
        $data = $request->validate([
            "email" => ["required", "email", "exists:users,email"]
        ], [
            "email.exists" => "Account not found"
        ]);
        $user = $this->userRepo->getUserDetails($data["email"]);
        $vrf = $this->vrfRepo->getVerificationCode($user, AccountEnums::passwordVerificationType);
        Notification::send($user, new VerificationCodeNotification($vrf));
        return ApiResponse::success("verification code sent", new VerificationResource($vrf));
    }

    public function resetPassword(ResetPasswordRequest $request){
        $data = $request->validated();
        $vrf = $this->vrfRepo->verifyToken($data["token"], true);
        if(!$vrf)
            abort(400, "Invalid request");
        $user = $vrf->user;
        $user = $this->userRepo->updatePassword($user, $data["password"]);
        Mail::to($user->email)->queue(new PasswordChangedMail($user));
        return ApiResponse::success("Password reset successful");
    }

}
