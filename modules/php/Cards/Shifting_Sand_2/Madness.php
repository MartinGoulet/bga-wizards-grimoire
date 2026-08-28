<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class Madness extends BaseCard {

    public function castSpell($args) {
        // This spell costs 2 less if the previous spell you cast cost 3 or more. Deal 2 damage
        $this->dealDamage(2);
    }

    public function getSpellDiscount() {
        return Globals::getSpellCost() >= 3 ? 2 : 0;
    }
}
