<?php

namespace App\Services\Transactions;

use App\DTOs\WalletTransDTO;
use App\DTOs\WalletTransLogDTO;
use App\Enums\BillingEnums;
use App\Enums\TransactionEnums;
use App\Models\Accounts\Wallet;
use App\Models\Accounts\WalletLog;
use App\Models\User;

class WalletService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function getAmountSpent($start_date = null, $end_date = null){
        $def = date("Y-m-d");
        $stDate = $start_date ?? $def;
        $endDate = $end_date ?? $def;
        $amt = WalletLog::where("transaction_type", TransactionEnums::debitTransType)
        ->where(function($query) use($stDate, $endDate){
            $query->where("created_at", ">=", $stDate)->where("created_at", "<=", $endDate);
        })
        ->sum("amount");
        return $amt;
    }
    public function checkWalletBalance(Wallet $wallet, $amount, $type = null){
        if($type == "blocked")
            $bal = $wallet->blocked_balance;
        else
            $bal = $wallet->balance;
        if($bal < $amount)
            abort(200, "Insufficient wallet balance. Fund your wallet and try again");

        $dailySpent = $this->getAmountSpent();
        if( $dailySpent >= $wallet->transaction_limit)
            abort(200, "Sorry!! You've exceeded your daily transaction limit.");
        return true;
    }

    public function creditWallet(Wallet $wallet, WalletTransDTO $data){
        $data->previous_balance = $wallet->balance;
        $data->previous_blocked_balance = $wallet->balance;
        $data->type = TransactionEnums::creditTransType;
        $wallet->balance += $data->amount;
        $wallet->save();
        $this->logWalletTransaction($wallet, $data);
        return $wallet;
    }
    public function debitWallet(Wallet $wallet, WalletTransDTO $data){
        $this->checkWalletBalance($wallet, $data->amount);
        $data->previous_balance = $wallet->balance;
        $data->previous_blocked_balance = $wallet->balance;
        $data->type = TransactionEnums::debitTransType;
        $wallet->balance -= $data->amount;
        $wallet->save();
        $this->logWalletTransaction($wallet, $data);
        return $wallet;
    }

    public function blockFunds(Wallet $wallet, WalletTransDTO $data){
        $this->checkWalletBalance($wallet, $data->amount);
        $data->previous_balance = $wallet->balance;
        $data->previous_blocked_balance = $wallet->balance;
        $data->type = TransactionEnums::blockedFundsTransType;
        $wallet->balance -= $data->amount;
        $wallet->blocked_balance += $data->amount;
        $wallet->save();
        $this->logWalletTransaction($wallet, $data);
        return $wallet;
    }

    public function refundFunds(Wallet $wallet, WalletTransDTO $data){
        $this->checkWalletBalance($wallet, $data->amount, "blocked");
        $data->previous_balance = $wallet->balance;
        $data->previous_blocked_balance = $wallet->balance;
        $data->type = TransactionEnums::refundedFundsTransType;
        $wallet->balance += $data->amount;
        $wallet->blocked_balance -= $data->amount;
        $wallet->save();
        $this->logWalletTransaction($wallet, $data);
        return $wallet;
    }

    public function chargeFunds(Wallet $wallet, WalletTransDTO $data){
        $this->checkWalletBalance($wallet, $data->amount, "blocked");
        $data->previous_balance = $wallet->balance;
        $data->previous_blocked_balance = $wallet->balance;
        $data->type = TransactionEnums::debitTransType;
        $wallet->blocked_balance -= $data->amount;
        $wallet->save();
        $this->logWalletTransaction($wallet, $data);
        return $wallet;
    }

    // private function creditBlockFunds(Wallet $wallet, WalletTransDTO $data)
    // {
    //     $data->previous_balance = $wallet->balance;
    //     $data->previous_blocked_balance = $wallet->balance;
    //     $data->type = TransactionEnums::creditTransType;
    //     $wallet->blocked_balance += $data->amount;
    //     $wallet->save();
    //     $this->logWalletTransaction($wallet, $data);
    //     return $wallet;
    // }

    public function transferBlockFunds(Wallet $from, Wallet $to, WalletTransDTO $walletDTO){
        $this->chargeFunds($from, $walletDTO);
        $this->creditWallet($to, $walletDTO);
    }

    private function logWalletTransaction(Wallet $wallet, WalletTransDTO $data){
        WalletLog::create([
            "wallet_id" => $wallet->id,
            "userid" =>$wallet->userid,
            "previous_balance" => $data->previous_balance,
            "previous_blocked_balance" => $data->previous_blocked_balance,
            "amount" => $data->amount,
            "new_balance" => $wallet->balance,
            "new_blocked_balance" => $wallet->blocked_balance,
            "log_rec" => $data->log_rec,
            "transaction_type" => $data->type,
            "reference" => $data->reference,
            "narration" => $data->narration,
        ]);
    }
}
