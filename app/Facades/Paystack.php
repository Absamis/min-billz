<?php

namespace App\Facades;

use Unicodeveloper\Paystack\Exceptions\PaymentVerificationFailedException;
use Unicodeveloper\Paystack\Paystack as PaystackPaystack;

class Paystack extends PaystackPaystack{

    /**
     * Get Payment details if the transaction was verified successfully
     * @return json
     * @throws PaymentVerificationFailedException
     */
    public function getPaymentData($ref = null)
    {
        if ($this->isTransactionVerificationValid($ref)) {

            return json_decode($this->response->getBody(), true);
        } else {
            throw new PaymentVerificationFailedException("Invalid Transaction Reference");
        }
    }
}
