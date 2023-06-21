<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class Hoodwink extends BaseCard {

    public function castSpell($args)
    {
        // Deal 7 damage, minus the damage your opponent dealt their last basic attack phase
        $this->dealDamage(7 - Globals::getPreviousBasicAttackPower());
    }
}
