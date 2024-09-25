<?php

namespace App\Services\Transactions;

use Illuminate\Support\Str;

class TransactionService{
    public static function getTransactionID(){
        $id = Str::random(15);
        return $id;
    }

    public static function getGiveawayCode(){
        $code = Str::upper(str_shuffle(Str::random(6).mt_rand(100,999)));
        return $code;
    }
}
