<?php

use App\Http\Controllers\Transactions\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("transactions/verify/{transaction}", [TransactionController::class, "verifyPaymentTransaction"])->name("web.trans.verify");
