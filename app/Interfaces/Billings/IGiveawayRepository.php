<?php

namespace App\Interfaces\Billings;

use App\Models\Billings\Giveaway;

interface IGiveawayRepository
{
    //
    public function createGiveaway($data);
    public function getGiveaways($code = null, $id = null, $filters = []);
    public function updateGiveawayStatus($code, Giveaway $gv = null, $status);
    public function claimGiveaway(Giveaway $gv, $data = []);
    public function findGiveaway($code);
}
