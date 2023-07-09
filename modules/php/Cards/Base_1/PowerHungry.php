<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class PowerHungry extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        Globals::setIsActivePowerHungry($value, $player_id);
    }
}
