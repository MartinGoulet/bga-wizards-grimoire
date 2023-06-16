<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Core\ManaCard;

class Tsunami extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. 
        // Deal 1 damage for each mana of a unique power in your hand

        $this->drawManaCards(1);

        $hand = ManaCard::getHand();

        $distinct_values = array_unique(array_values(array_filter($hand, function ($card) {
            return intval($card['type']);
        })));

        $this->dealDamage(sizeof($distinct_values));
    }
}
