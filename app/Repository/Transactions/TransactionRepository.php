<?php

namespace App\Repository\Transactions;

use App\DTOs\InitPaymentDTO;
use App\DTOs\TransactionDTO;
use App\DTOs\WalletTransDTO;
use App\Enums\AppEnums;
use App\Enums\TransactionEnums;
use App\Interfaces\Transactions\ITransactionRepository;
use App\Models\Billings\BillingTransaction;
use App\Models\ReferralBonus;
use App\Models\Transactions\Transaction;
use App\Models\User;
use App\Services\PaymentGateway\PaymentService;
use App\Services\Transactions\WalletService;
use App\Services\Transactions\TransactionService;

class TransactionRepository implements ITransactionRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function initiatePaymentTransaction(TransactionDTO $transDT, $transType){
        $ref = TransactionService::getTransactionID();
        $payDTO = new InitPaymentDTO(
            amount: $transDT->amount,
            reference: $ref,
            customerEmail: auth()->user()->email,
            metadata: [
                "trans_type" => $transType,
                "narration" => TransactionEnums::narrations[$transType]
            ],
            customerName: auth()->user()->name,
        );
        $payData = PaymentService::initiateTransaction($transDT->payment_gateway, $payDTO);
        $transData = $this->saveTransaction($transDT->amount, $transType, $transDT, $ref);
        return ["payment_data" => $payData, "transaction_data" => $transData];
    }

    public function verifyPaymentTransaction(Transaction $trans, $data){
        $resp = PaymentService::verifyTransaction($trans->payment_gateway, $data);
        if($trans->status != TransactionEnums::pendingStatus)
            abort(200, "Transaction is already verified");
        if($resp->amount < $trans->amount)
            $resp->status = TransactionEnums::failedStatus;
        $trans->status = $resp->status;
        $trans->charges = $resp->charges;
        $trans->payment_reference = $resp->pay_reference;
        $trans->transaction_date = $resp->transDate;
        $trans->narration = $resp->metadata["narration"] ?? null;
        $trans->data = $resp->data;
        $trans->save();
        return $trans;
        // return ApiResponse::success("Verified", $resp);
    }

    public function processTransaction(Transaction $trans){
        switch($trans->transaction_type){
            case TransactionEnums::depositTransType:
                $trans = $this->fundWallet($trans);
                break;
            default:
                break;
        }
        return $trans;
    }

    private function fundWallet(Transaction $trans){
        if($trans->transaction_type != TransactionEnums::depositTransType || $trans->status != TransactionEnums::successStatus)
            return $trans;
        $user = $trans->user;
        $referrer = $user->referrer;
        $walletService = new WalletService();
        if($referrer){
            $refBon = ReferralBonus::active()->where(["userid" => $referrer->id, "referee_id" => $user->id])->irst();
            if($refBon){
                $trAmt = $trans->amount;
                $entBon = $refBon->entitled_bonus;
                $bonEarned = $refBon->bonus_earned;
                $percCharge = $refBon->percentage_charge;
                if($bonEarned < $entBon){
                    $bon = ($percCharge/100) * $trAmt;
                    $totBonEarned = $bonEarned + $bon;
                    if($totBonEarned > $entBon){
                        $totBonEarned = $entBon;
                        $bon = $entBon - $bonEarned;
                    }
                    $bonEarned = $totBonEarned;
                    $refBon->bonus_earned = $bonEarned;
                    $refBon->save();
                    $walletService->creditWallet($referrer->wallet, new WalletTransDTO(
                        amount: $bon,
                        log_rec: TransactionEnums::referralBonusRec,
                        narration: "Referral bonus for $user->user_tag",
                        reference: $trans::class."|$trans->id"
                    ));
                }
            }

        }

        $walletService->creditWallet($user->wallet, new WalletTransDTO(
            amount: $trans->amount,
            log_rec: TransactionEnums::depositTransType,
            narration: "Wallet funded",
            reference: $trans::class . "|$trans->id"
        ));
        return $trans;
    }

    private function saveTransaction($amount, $type, TransactionDTO $data, $transID = null, User $user = null){
        $user = $user ?? auth()->user();
        $transID = $transID ?? TransactionService::getTransactionID();
        $trans = Transaction::create([
            "id" => $transID,
            "userid" => $user->id,
            "amount" => $amount,
            "currency" => appSettings()->app_currency,
            "transaction_type" => $type,
            "payment_method_id" => $data->payment_method_id,
            "payment_gateway" => $data->payment_gateway,
            "payment_channel" => $data->payment_channel,
            "payment_reference" => $data->payment_reference,
            "transaction_date" => $data->transaction_date,
            "narration" => $data->narration,
            "data" => $data->data,
            "status" => $data->status
        ]);
        return $trans;
    }

    public function getBillingsTransactions($filters = []){
        $type = $filters["type"] ?? null;
        $stDate = $filters["start_date"] ?? null;
        $endDate = $filters["end_date"] ?? null;
        $status = $filters["status"] ?? null;
        $data = BillingTransaction::query()->where("userid", auth()->user()->id);
        if($type)
            $data = $data->where("transaction_type", $type);
        if($stDate)
            $data = $data->where("created_at", ">=", $stDate);
        if($endDate)
            $data = $data->where("created_at", "<=", $endDate);
        if($status)
            $data = $data->where("status", $status);
        $data = $data->orderBy("created_at", "DESC")->paginate(pageCount());
        return $data;
    }

    public function getTransactions($filters = [])
    {
        $type = $filters["type"] ?? null;
        $stDate = $filters["start_date"] ?? null;
        $endDate = $filters["end_date"] ?? null;
        $status = $filters["status"] ?? null;
        $data = Transaction::query()->where("userid", auth()->user()->id);
        if ($type)
            $data = $data->where("transaction_type", $type);
        if ($stDate)
            $data = $data->where("created_at", ">=", $stDate);
        if ($endDate)
            $data = $data->where("created_at", "<=", $endDate);
        if ($status)
            $data = $data->where("status", $status);
        $data = $data->orderBy("created_at", "DESC")->paginate(pageCount());
        return $data;
    }
}
