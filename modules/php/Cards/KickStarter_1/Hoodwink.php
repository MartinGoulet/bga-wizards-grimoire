<?php

namespace WizardsGrimoireExt\Cards\KickStarter_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class Hoodwink extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage, minus the damage your opponent dealt their last basic attack phase
        $this->dealDamage(6 - Globals::getLastBasicAttackDamage());
    }
}
