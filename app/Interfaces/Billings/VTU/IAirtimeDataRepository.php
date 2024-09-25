<?php

namespace App\Interfaces\Billings\VTU;

use App\Models\Billings\BillingTransaction;

interface IAirtimeDataRepository
{
    //
    public function getDataPlans();
    public function buyData($plan_id, $phone): BillingTransaction;
    public function buyAirtime($prov_id, $amount, $phone): BillingTransaction;
}
