<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class PowerHungry extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        Globals::setIsActivePowerHungry($value, $player_id);
    }
}
