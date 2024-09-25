<?php

namespace App\DTOs\VTU;

class VTUTransactionDTO
{
    /**
     * Create a new class instance.
     */
    public $serviceID;
    public $amount;
    public $planID;
    public $reference;
    public $recipient;
    public $merchant_id;
    public function __construct(
        $amount,
        $recipient,
        $reference,
         $serviceID = null,
        $planID = null,
        $merchant_id = null,
    )
    {
        //
        $this->amount = $amount;
        $this->serviceID = $serviceID;
        $this->planID = $planID;
        $this->recipient = $recipient;
        $this->merchant_id = $merchant_id;
        $this->reference = $reference;

    }
}
