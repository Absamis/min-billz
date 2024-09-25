<?php

namespace App\Http\Controllers\Transactions;

use App\Classes\ApiResponse;
use App\DTOs\TransactionDTO;
use App\Enums\TransactionEnums;
use App\Events\WalletFunded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\FundWalletRequest;
use App\Http\Resources\TransactionResource;
use App\Interfaces\Transactions\ITransactionRepository;
use App\Models\Transactions\Transaction;
use App\Services\PaymentGateway\PaymentService;
use App\Services\Transactions\TransactionService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public ITransactionRepository $transRepo;
    public function __construct(ITransactionRepository $trsRepo)
    {
        $this->transRepo = $trsRepo;
    }
    public function initateFundWallet(FundWalletRequest $request){
        $data = $request->validated();
        $trs = new TransactionDTO(
            amount: $data["amount"],
            payment_method_id: $data["payment_method_id"]
        );
        $resp = $this->transRepo->initiatePaymentTransaction($trs,  TransactionEnums::depositTransType);
        return ApiResponse::success("Transaction successfully initiated", $resp);
    }

    public function verifyPaymentTransaction(Request $request, Transaction $transaction){
        $trans = $this->transRepo->verifyPaymentTransaction($transaction, [
            "reference" => $transaction->id
        ]);
        if ($trans->status == TransactionEnums::failedStatus)
            abort(200, "Transaction failed", ["data" => $trans]);
        $resp =$this->transRepo->processTransaction($trans);
        WalletFunded::dispatch($resp);
        return ApiResponse::success("Transaction processed succesfully", new TransactionResource($resp));
    }

    public function getBillingTransactions(Request $request){
        $filters = $request->all();
        $resp = $this->transRepo->getBillingsTransactions($filters);
        return ApiResponse::success("Transaction fetched", $resp);
    }

    public function getTransactions(Request $request)
    {
        $filters = $request->all();
        $resp = $this->transRepo->getTransactions($filters);
        return ApiResponse::success("Transactions fetched", $resp);
    }
}
