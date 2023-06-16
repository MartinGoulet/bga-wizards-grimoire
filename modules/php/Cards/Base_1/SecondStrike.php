<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class SecondStrike extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. 
        $this->dealDamage(3);
        // The next time you cast an attack spell this turn, it costs 1 less.
        Globals::setDiscountAttackSpell(1);
    }
}
