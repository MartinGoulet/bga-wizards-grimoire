<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class EnergyStorm extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. Deal 1 additional damage for each 4 power mana in your hand
        $mana_cards = ManaCard::getHand();

        $nbr_4_powers = sizeof(array_filter($mana_cards, function ($card) {
            return ManaCard::getPower($card) == 4;
        }));

        $this->dealDamage(3 + $nbr_4_powers);
    }
}
