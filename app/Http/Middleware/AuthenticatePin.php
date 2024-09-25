<?php

namespace App\Http\Middleware;

use App\Interfaces\IUserProfileRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePin
{

    public $userRepo;
    public function __construct(IUserProfileRepository $uRepo)
    {
        $this->userRepo = $uRepo;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pin = $request->header("x-auth-pin");
        !$pin ? abort(400, "Pin is required") : null;
        $this->userRepo->validatePin(["pin" => $pin]);
        return $next($request);
    }
}
