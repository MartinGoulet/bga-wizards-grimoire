<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\ManaCard;

class Hellstorm extends BaseCard {

    public function castSpell($args) {
        // Gain 5 mana cards. Deal damage equal to the highest power mana you gain
        $cards = $this->drawManaCards(5);

        $max_value = ManaCard::getMaxValue($cards);
        $this->dealDamage($max_value);
    }
}
