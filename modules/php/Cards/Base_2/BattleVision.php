<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class BattleVision extends BaseCard {

    public function isOngoingSpellActive(bool $value) {
        // When your opponent basic attacks, you may discard a mana card of the same power from your hand to block the damage
        Globals::setIsActiveBattleVision($value);
    }
}
