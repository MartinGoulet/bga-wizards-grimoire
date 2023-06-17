<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class Growth extends BaseCard {

    // During your turn, increase the power of all mana by 1

    public function isOngoingSpellActive(bool $value)
    {
        Globals::setIsActiveGrowth($value);
    }
}
