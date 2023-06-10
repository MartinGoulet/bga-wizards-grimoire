<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Objects\CardLocation;

class TranceState extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage, minus 1 damage for each mana card in your hand
        $hand_count = ManaCard::getHandCount();

        $damage = max([3 - $hand_count, 0]);
        $this->dealDamage($damage);
    }
}
