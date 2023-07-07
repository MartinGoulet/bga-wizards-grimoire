<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class Tsunami extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. 
        // Deal 1 damage for each mana of a unique power in your hand

        $this->drawManaCards(1);

        $hand = ManaCard::getHand();

        $values = array_values(array_map(function ($card) {
            return ManaCard::getPower($card);
        }, $hand));

        $distinct_values = array_unique($values);

        $this->dealDamage(sizeof($distinct_values));
    }
}
