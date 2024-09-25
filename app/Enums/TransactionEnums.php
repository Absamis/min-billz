<?php

namespace App\Enums;

enum TransactionEnums
{
    //
    const pendingStatus = "pending";
    const successStatus = "successful";
    const failedStatus = "failed";

    const depositTransType = "deposit";
    const creditTransType = "credit";
    const debitTransType = "debit";
    const blockedFundsTransType = "blocked";
    const refundedFundsTransType = "refunded";

    const referralBonusRec = "referral-bonus";
    // const transferFundRec = ""

    const paymentMethods = [
        "Paystack" => "paystack",
        "Wallet" => "wallet"
    ];

    const narrations = [
        "deposit" => "Fund wallet"
    ];

    const currenciesSymbol = [
        "NGN" => "&#8358;"
    ];
}
