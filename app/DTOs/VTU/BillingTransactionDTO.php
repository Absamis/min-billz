<?php

namespace App\DTOs\VTU;

class BillingTransactionDTO
{
    /**
     * Create a new class instance.
     */
    public $id;
    public $userid;
    public $amount = 0;
    public $vendor_amount = 0;
    public $charges = 0;
    public $currency;
    public $transaction_type;
    public $vendor;
    public $service_type;
    public $reference;
    public $service_name;
    public $service_id;
    public $recipient;
    public $payment_method;
    public $narration;
    public $data;
    public $status;
    public $respCode;
    public function __construct(
        $vendor_amount,
        $reference, $status,
        $recipient,
        $vendor,
        $respCode = null,
        $id = null,
        $data = null,
    )
    {
        //
        $this->respCode = $respCode;
        $this->id = $id;
        $this->vendor_amount = $vendor_amount;
        $this->vendor = $vendor;
        $this->recipient = $recipient;
        $this->reference = $reference;
        $this->data = $data;
        $this->status = $status;
    }
}
