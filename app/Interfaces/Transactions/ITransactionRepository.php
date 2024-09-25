<?php

namespace App\Interfaces\Transactions;

use App\DTOs\TransactionDTO;
use App\Models\Transactions\Transaction;

interface ITransactionRepository
{
    //
    public function initiatePaymentTransaction(TransactionDTO $data, $transType);
    public function verifyPaymentTransaction(Transaction $trans, $data);
    public function processTransaction(Transaction $trans);
    public function getBillingsTransactions($filters = []);
    public function getTransactions($filters = []);
}
