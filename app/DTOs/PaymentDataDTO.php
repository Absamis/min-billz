<?php

namespace App\DTOs;

class PaymentDataDTO
{
    /**
     * Create a new class instance.
     */
    public $pay_reference;
    public $metadata;
    public $transDate;
    public $id;
    public $amount;
    public $name;
    public $email;
    public $status;
    public $data;
    public $charges;
    public function __construct(
        $pay_reference = null,
        $metadata = null,
        $transDate = null,
        $id = null,
        $email = null,
        $amount = 0,
        $status,
        $data = null,
        $charges = 0
        )
    {
        //
        $this->pay_reference = $pay_reference;
        $this->metadata = $metadata;
        $this->transDate = $transDate;
        $this->id = $id;
        $this->amount = $amount;
        $this->email = $email;
        $this->status = $status;
        $this->data = $data;
        $this->charges = $charges;
    }
}
