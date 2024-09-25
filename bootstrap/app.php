<?php

use App\Classes\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            "auth.pin" => \App\Http\Middleware\AuthenticatePin::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function(ValidationException $validator){
            return ApiResponse::failed("Fill in appropriate details", [], $validator->errors(), 400);
        });

        $exceptions->render(function (HttpException $ex) {
            return ApiResponse::failed($ex->getMessage(), $ex->getHeaders()['data'] ?? [], [], $ex->getStatusCode());
        });

        $exceptions->render(function (Exception $ex) {
            return ApiResponse::failed("Error occured.", $ex->getMessage().$ex->getTraceAsString(), []);
        });

        $exceptions->report(function (Exception $ex) {
            return ApiResponse::failed("Error occured.", $ex->getMessage() . $ex->getTraceAsString(), []);
        });

    })->create();
