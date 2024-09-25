<?php

namespace App\Enums;

use App\Models\Billings\Giveaway;

enum BillingEnums
{
    //
    const airtimePurchaseType = "airtime-purchase";
    const dataPurchaseType = "data-purchase";

    const airtimeType = "airtime";
    const dataBundleType = "data";
    const giveawayType = "giveaway";

    const activeGiveaway = "active";
    const expiredGiveaway = "expired";
    const canceledGiveaway = "canceled";
    const inactiveGiveaway = "inactive";
}
