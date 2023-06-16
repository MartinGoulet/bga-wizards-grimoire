<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Core\ManaCard;

class TranceState extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage, minus 1 damage for each mana card in your hand
        $hand_count = ManaCard::getHandCount();

        $damage = max([3 - $hand_count, 0]);
        $this->dealDamage($damage);
    }
}
