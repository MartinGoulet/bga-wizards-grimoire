<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class PowerHungry extends BaseCard {

    public function isOngoingSpellActive(bool $value) {
        Globals::setIsActivePowerHungry($value);
    }
}
