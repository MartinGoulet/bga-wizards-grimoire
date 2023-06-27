<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class BloodLust extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Deal an additionnal 2 damage for each attack spell cast consecutively before this spell
        $damage = 1 + (Globals::getConsecutivelyAttackSpellCount() - 1) * 2;
        $this->dealDamage($damage);
    }
}
