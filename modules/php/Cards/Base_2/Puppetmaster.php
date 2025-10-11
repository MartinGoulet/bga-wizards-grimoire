<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class Puppetmaster extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        // In order to basic attack, your opponent must use a mana of the same power as you did during the previous basic attack phase
        Globals::setIsActivePuppetMaster($value, $player_id);
    }
}
