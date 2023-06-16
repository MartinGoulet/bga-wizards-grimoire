<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class SneakyDeal extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Deal 1 damage, or discard a mana card off 1 of your other spells
        if ($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            $position = intval(array_shift($args));
            ManaCard::discardManaFromSpell($position);
        }
    }
}
