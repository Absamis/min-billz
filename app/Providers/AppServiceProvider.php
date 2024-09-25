<?php

namespace App\Providers;

use App\Events\NewBillsPayment;
use App\Events\UserAccountVerified;
use App\Events\UserRegisteredWithEmail;
use App\Events\WalletFunded;
use App\Http\Middleware\Authenticate as MiddlewareAuthenticate;
use App\Listeners\CreateWalletAccount;
use App\Listeners\SendAccountVerificationCode;
use App\Listeners\SendNewBillsPaymentNotification;
use App\Listeners\SendWalletFundedNotification;
use App\Listeners\SendWelcomeMessage;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(Authenticate::class, MiddlewareAuthenticate::class);
        $this->app->bind('laravel-paystack', function () {
            return new \App\Facades\Paystack;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        //
        Dispatcher::macro('listeners', function ($events, array $listeners = []) {
            array_map(function ($listener) use ($events) {
                Event::listen($events, $listener);
            }, $listeners);
        });

        Event::listen(
            UserRegisteredWithEmail::class, SendAccountVerificationCode::class,
        );
        Event::listeners(
            UserAccountVerified::class, [
                SendWelcomeMessage::class,
                CreateWalletAccount::class
            ]
        );
        Event::listen(
            WalletFunded::class, SendWalletFundedNotification::class
        );

        Event::listen(
            NewBillsPayment::class, SendNewBillsPaymentNotification::class
        );

        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
    }
}
