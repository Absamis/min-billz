<?php

namespace App\DTOs;

use App\Enums\TransactionEnums;
use App\Models\Transactions\PaymentMethod;

class TransactionDTO
{
    /**
     * Create a new class instance.
     */
    public $payment_method_id;
    public $payment_gateway;
    public $payment_channel;
    public $payment_reference;
    public $transaction_date;
    public $narration;
    public $data;
    public $amount;
    public $status;
    public function __construct(
        $amount = 0,
        $payment_method_id = null,
        $payment_channel = null,
        $payment_reference = null,
        $transaction_date = null,
        $narration = null,
        $data = null,
        $status = null
    )
    {
        //
        $this->amount = $amount;
        $this->payment_method_id = $payment_method_id;
        $this->payment_gateway = PaymentMethod::find($payment_method_id)->code ?? null;
        $this->payment_channel = $payment_channel;
        $this->payment_reference = $payment_reference;
        $this->transaction_date = $transaction_date;
        $this->narration = $narration;
        $this->data = $data;
        $this->status = $status ?? TransactionEnums::pendingStatus;
    }
}
