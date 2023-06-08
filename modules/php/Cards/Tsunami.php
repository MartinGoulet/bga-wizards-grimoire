<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Players;

class Tsunami extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. 
        // Deal 1 damage for each mana of a unique power in your hand

        $this->drawManaCards(1);

        $hand = Players::getHand();

        $distinct_values = array_unique(array_values(array_filter($hand, function ($card) {
            return intval($card['type']);
        })));

        $this->dealDamage(sizeof($distinct_values));
    }
}
