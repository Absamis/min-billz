<?php

namespace App\DTOs;

use App\Enums\TransactionEnums;

class InitPaymentDTO
{
    /**
     * Create a new class instance.
     */
    public $amount;
    public $reference;
    public $currency;
    public $metadata;
    public $subAccount;
    public $customerName;
    public $customerEmail;
    public $successCallbackUrl;
    public $failedCallbackUrl;
    public function __construct(
        $amount = 0,
        $customerEmail = null,
        $reference = null,
        $metadata = [],
        $customerName = null,
        $currency = null,
        $subAccount = null,
        $successCallbackUrl = null,
        $failedCallbackUrl = null,
        )
    {
        //
        $this->amount = $amount;
        $this->reference = $reference;
        $this->currency = $currency ?? appSettings()->app_currency;
        $this->metadata = $metadata;
        $this->subAccount = $subAccount;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->successCallbackUrl = $successCallbackUrl;
        $this->failedCallbackUrl = $failedCallbackUrl;
    }
}
