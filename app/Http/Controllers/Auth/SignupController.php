<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Enums\AccountEnums;
use App\Events\UserAccountVerified;
use App\Events\UserRegisteredWithEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupWithEmailRequest;
use App\Http\Requests\Auth\VerifyAccountRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Interfaces\Auth\IVerificationRepository;
use App\Interfaces\IReferralRepository;
use App\Interfaces\IUserRepository;
use App\Models\User;
use App\Services\Apis\MonnifyApiService;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    //
    public IVerificationRepository $vrfRepo;
    public IUserRepository $userRepo;
    public IReferralRepository $refRepo;
    public function __construct(IVerificationRepository $vrf, IUserRepository $uRepo, IReferralRepository $refRepo)
    {
        $this->vrfRepo = $vrf;
        $this->refRepo = $refRepo;
        $this->userRepo = $uRepo;
    }
    public function signupWithEmail(SignupWithEmailRequest $request){
        $data = $request->validated();
        $referrer = $this->userRepo->getTagUser($data["referral_tag"] ?? null);
        $referrer ? $data["referrer_id"] = $referrer->id : null;
        $user = $this->userRepo->createNewAccount($data);
        // Save referrer
        if($referrer){
            $refs = $this->refRepo->addReferralData($referrer, $user);
        }
        //Verify Registration
        $vrf = $this->vrfRepo->getVerificationCode($user, AccountEnums::accountVerificationType);
        UserRegisteredWithEmail::dispatch($user, $vrf);
        return ApiResponse::success("Registration successful", new VerificationResource($vrf));
    }

    public function testPay(MonnifyApiService $srv){
        //return $srv->createReserveAccounts(User::first());
    }
}
