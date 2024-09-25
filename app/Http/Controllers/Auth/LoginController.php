<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Enums\AccountEnums;
use App\Events\UserRegisteredWithEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Http\Resources\UserResource;
use App\Interfaces\Auth\IVerificationRepository;
use App\Interfaces\IUserRepository;
use App\Mail\SendNewLoginMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    //

    public IVerificationRepository $vrfRepo;
    public IUserRepository $userRepo;
    public function __construct(IVerificationRepository $vrf, IUserRepository $userRepo)
    {
        $this->vrfRepo = $vrf;
        $this->userRepo = $userRepo;
    }

    public function loginWithEmail(LoginRequest $request){
        $data = $request->validated();
        $user = $this->userRepo->getUserDetails($data["email"]);
        $valid = $this->userRepo->validatePassword($user, $data["password"]);
        !$valid ? abort(401, "Invalid login details") : null;
        if($user->isUnverified){
            $vrf = $this->vrfRepo->getVerificationCode($user, AccountEnums::accountVerificationType);
            UserRegisteredWithEmail::dispatch($user, $vrf);
            return ApiResponse::success("Verify your account", new VerificationResource($vrf), [], 403);
        }elseif(!$user->isActive){
            return abort(403, "Your account is suspended. kindly contact support");
        }
        $user = $this->userRepo->loginUser($user);
        Mail::to($user->email)->queue(new SendNewLoginMail($user));
        return ApiResponse::success("Login successful", new UserResource($user));
    }
}
