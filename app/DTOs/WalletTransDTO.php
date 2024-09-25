<?php

namespace App\DTOs;

class WalletTransDTO
{
    /**
     * Create a new class instance.
     */
    public $amount;
    public $narration;
    public $type;
    public $log_rec;
    public $reference;
    public $previous_balance;
    public $previous_blocked_balance;
    public $new_blocked_balance;
    public $new_balance;
    public function __construct($amount, $log_rec, $narration = null, $reference = null)
    {
        //
        $this->amount = $amount;
        $this->narration = $narration;
        $this->log_rec = $log_rec;
        $this->reference = $reference;
    }
}
